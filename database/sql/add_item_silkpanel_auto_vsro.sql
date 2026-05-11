CREATE OR ALTER PROCEDURE [dbo].[_ADD_ITEM_SILKPANEL_AUTO_VSRO]
    @CharName      VARCHAR(64)  = NULL,
    @CharID        INT          = NULL,
    @CodeName      VARCHAR(128) = NULL,
    @RefItemID     INT          = NULL,
    @Data          INT          = 1,
    @OptLevel      TINYINT      = 0,
    @Destination   VARCHAR(10)  = NULL OUTPUT,
    @Slot          INT          = 0   OUTPUT,
    @NewItemID     BIGINT       = 0   OUTPUT
AS
BEGIN
    SET NOCOUNT ON;
    SET XACT_ABORT ON;

    IF @RefItemID IS NULL
    BEGIN
        IF @CodeName IS NULL
        BEGIN
            RAISERROR('Either CodeName or RefItemID must be provided.', 16, 1);
            RETURN -1;
        END
        SELECT @RefItemID = ID FROM _RefObjCommon WITH (NOLOCK) WHERE Codename128 = @CodeName;
        IF @RefItemID IS NULL RETURN -1;
    END
    ELSE IF NOT EXISTS (SELECT 1 FROM _RefObjCommon WITH (NOLOCK) WHERE ID = @RefItemID)
        RETURN -1;

    DECLARE @InvSize INT = 45;
    IF @CharID IS NULL
    BEGIN
        IF @CharName IS NULL
        BEGIN
            RAISERROR('Either CharName or CharID must be provided.', 16, 1);
            RETURN -2;
        END
        SELECT @CharID = CharID, @InvSize = ISNULL(InventorySize, 45)
        FROM _Char WITH (NOLOCK) WHERE CharName16 = @CharName;
        IF @CharID IS NULL RETURN -2;
    END
    ELSE
    BEGIN
        SELECT @InvSize = ISNULL(InventorySize, 45) FROM _Char WITH (NOLOCK) WHERE CharID = @CharID;
        IF @InvSize IS NULL RETURN -2;
    END

    DECLARE @UserJID INT;
    SELECT @UserJID = UserJID FROM _User WITH (NOLOCK) WHERE CharID = @CharID;
    IF @UserJID IS NULL RETURN -5;

    DECLARE @Link INT, @Type1 TINYINT, @Type2 TINYINT, @Type3 TINYINT, @Type4 TINYINT;
    SELECT @Link = Link, @Type1 = TypeID1, @Type2 = TypeID2, @Type3 = TypeID3, @Type4 = TypeID4
    FROM _RefObjCommon WITH (NOLOCK) WHERE ID = @RefItemID;

    IF @Link IS NULL OR @Link = 0 RETURN -5;
    IF @Type1 <> 3 RETURN -6;

    IF @Type1 = 3 AND @Type2 = 1
    BEGIN
        SELECT @Data = Dur_L FROM _RefObjItem WITH (NOLOCK) WHERE ID = @Link;
        IF @OptLevel < 0 SET @OptLevel = 0 ELSE IF @OptLevel > 15 SET @OptLevel = 15;
    END
    ELSE IF @Type1 = 3 AND @Type2 = 2 AND @Type3 = 1 AND @Type4 IN (1,2)
        SET @Data = 0;
    ELSE
    BEGIN
        DECLARE @MaxStack INT;
        SELECT @MaxStack = MaxStack FROM _RefObjItem WITH (NOLOCK) WHERE ID = @Link;
        IF @Data <= 0 OR @Data > @MaxStack SET @Data = @MaxStack;
        SET @OptLevel = 0;
    END

    DECLARE @FreeSlot INT;
    SET @FreeSlot = NULL;

    SELECT TOP 1 @FreeSlot = Slot FROM _Inventory WITH (NOLOCK)
    WHERE CharID = @CharID AND Slot >= 13 AND Slot < @InvSize AND ItemID = 0
    ORDER BY Slot;

    IF @FreeSlot IS NOT NULL
    BEGIN
        SET @Destination = 'Inventory';
        SET @Slot = @FreeSlot;
    END
    ELSE
    BEGIN
        DECLARE @ChestSize INT = 150;
        SELECT @ChestSize = ChestSize FROM _ChestInfo WITH (NOLOCK) WHERE JID = @UserJID;
        IF @ChestSize IS NULL SET @ChestSize = 150;

        SELECT TOP 1 @FreeSlot = Slot FROM _Chest WITH (NOLOCK)
        WHERE UserJID = @UserJID
          AND Slot < @ChestSize
          AND (ItemID = 0 OR ItemID IS NULL)
        ORDER BY Slot;

        IF @FreeSlot IS NOT NULL
        BEGIN
            SET @Destination = 'Storage';
            SET @Slot = @FreeSlot;
        END
        ELSE
            RETURN -4;
    END

    BEGIN TRANSACTION
    BEGIN TRY
        DECLARE @NewSerial BIGINT;

        UPDATE _LatestItemSerial SET LatestItemSerial = LatestItemSerial + 1;
        SELECT @NewSerial = LatestItemSerial FROM _LatestItemSerial WITH (UPDLOCK);

        INSERT INTO _Items (RefItemID, OptLevel, Data, MagParamNum, Serial64)
        VALUES (@RefItemID, @OptLevel, @Data, 0, @NewSerial);

        SET @NewItemID = SCOPE_IDENTITY();

        IF @Destination = 'Inventory'
            UPDATE _Inventory SET ItemID = @NewItemID WHERE CharID = @CharID AND Slot = @FreeSlot;
        ELSE
            UPDATE _Chest SET ItemID = @NewItemID WHERE UserJID = @UserJID AND Slot = @FreeSlot;

        INSERT INTO _ItemPool (InUse, ItemID) VALUES (1, @NewItemID);

        COMMIT TRANSACTION;
        RETURN 1;
    END TRY
    BEGIN CATCH
        ROLLBACK TRANSACTION;
        SET @Destination = NULL;
        SET @Slot = 0;
        SET @NewItemID = 0;
        RETURN -7;
    END CATCH
END