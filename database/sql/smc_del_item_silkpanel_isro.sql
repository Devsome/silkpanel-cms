CREATE OR ALTER PROCEDURE [dbo].[_SMC_DEL_ITEM_SILKPANEL_ISRO]
    @TargetStorage  INT,            -- 0=Inventory, 1=Chest, 2=GuildChest, 3=AvatarInv, 4=TradeBag, 5=TradeEquip
    @OwnerName      VARCHAR(128),
    @Unknown        INT = 0,        -- PX2000, wird i.d.R. nicht verwendet
    @Slot           INT
AS
BEGIN
    SET NOCOUNT ON;
    SET XACT_ABORT ON;

    -- 1. Gültigen Storage-Typ prüfen (erweitert um 4 und 5)
    IF @TargetStorage NOT IN (0, 1, 2, 3, 4, 5)
    BEGIN
        SELECT -12;
        RETURN;
    END

    DECLARE @OwnerID INT = NULL;
    DECLARE @ItemToDel BIGINT = NULL;

    -- 2. Owner-ID und ItemID ermitteln
    IF @TargetStorage = 0             -- Inventar
    BEGIN
        SELECT @OwnerID = CharID FROM _Char WHERE CharName16 = @OwnerName;
        IF @@ROWCOUNT = 0 OR @OwnerID IS NULL BEGIN SELECT -2; RETURN; END

        SELECT @ItemToDel = ItemID FROM _Inventory WHERE CharID = @OwnerID AND Slot = @Slot;
        IF @@ROWCOUNT = 0 OR @ItemToDel = 0 BEGIN SELECT -3; RETURN; END
    END
    ELSE IF @TargetStorage = 1        -- Chest
    BEGIN
        -- iSRO-spezifisch: UserJID über _User und _Char ermitteln
        SELECT @OwnerID = UserJID
        FROM _User
        WHERE CharID = (SELECT CharID FROM _Char WHERE CharName16 = @OwnerName);

        IF @@ROWCOUNT = 0 OR @OwnerID IS NULL
        BEGIN
            SELECT -2;
            RETURN;
        END

        SELECT @ItemToDel = ItemID FROM _Chest WHERE UserJID = @OwnerID AND Slot = @Slot;
        IF @@ROWCOUNT = 0 OR @ItemToDel = 0 BEGIN SELECT -3; RETURN; END
    END
    ELSE IF @TargetStorage = 2        -- Gilden-Chest
    BEGIN
        SELECT @OwnerID = ID FROM _Guild WHERE Name = @OwnerName;
        IF @@ROWCOUNT = 0 OR @OwnerID IS NULL BEGIN SELECT -2; RETURN; END

        SELECT @ItemToDel = ItemID FROM _GuildChest WHERE GuildID = @OwnerID AND Slot = @Slot;
        IF @@ROWCOUNT = 0 OR @ItemToDel = 0 BEGIN SELECT -3; RETURN; END
    END
    ELSE IF @TargetStorage = 3        -- Avatar-Inventar
    BEGIN
        SELECT @OwnerID = CharID FROM _Char WHERE CharName16 = @OwnerName;
        IF @@ROWCOUNT = 0 OR @OwnerID IS NULL BEGIN SELECT -2; RETURN; END

        SELECT @ItemToDel = ItemID FROM _InventoryForAvatar WHERE CharID = @OwnerID AND Slot = @Slot;
        IF @@ROWCOUNT = 0 OR @ItemToDel = 0 BEGIN SELECT -3; RETURN; END
    END
    ELSE IF @TargetStorage = 4        -- Trade Bag
    BEGIN
        SELECT @OwnerID = CharID FROM _Char WHERE CharName16 = @OwnerName;
        IF @@ROWCOUNT = 0 OR @OwnerID IS NULL BEGIN SELECT -2; RETURN; END

        SELECT @ItemToDel = ItemID FROM _TradeBagInventory WHERE CharID = @OwnerID AND Slot = @Slot;
        IF @@ROWCOUNT = 0 OR @ItemToDel = 0 BEGIN SELECT -3; RETURN; END
    END
    ELSE IF @TargetStorage = 5        -- Trade Equip
    BEGIN
        SELECT @OwnerID = CharID FROM _Char WHERE CharName16 = @OwnerName;
        IF @@ROWCOUNT = 0 OR @OwnerID IS NULL BEGIN SELECT -2; RETURN; END

        SELECT @ItemToDel = ItemID FROM _TradeEquipInventory WHERE CharID = @OwnerID AND Slot = @Slot;
        IF @@ROWCOUNT = 0 OR @ItemToDel = 0 BEGIN SELECT -3; RETURN; END
    END

    -- 3. Sicherheitscheck
    IF @ItemToDel IS NULL OR @ItemToDel = 0
    BEGIN
        SELECT -4;
        RETURN;
    END

    -- 4. Transaktion: Slot leeren (DELETE, da REFACTORING_INVENTORY aktiv), Rent entfernen, Item freigeben
    BEGIN TRY
        BEGIN TRANSACTION;

        IF @TargetStorage = 0
            DELETE FROM _Inventory WHERE CharID = @OwnerID AND Slot = @Slot;
        ELSE IF @TargetStorage = 1
            DELETE FROM _Chest WHERE UserJID = @OwnerID AND Slot = @Slot;
        ELSE IF @TargetStorage = 2
            DELETE FROM _GuildChest WHERE GuildID = @OwnerID AND Slot = @Slot;
        ELSE IF @TargetStorage = 3
            DELETE FROM _InventoryForAvatar WHERE CharID = @OwnerID AND Slot = @Slot;
        ELSE IF @TargetStorage = 4
            DELETE FROM _TradeBagInventory WHERE CharID = @OwnerID AND Slot = @Slot;
        ELSE
            DELETE FROM _TradeEquipInventory WHERE CharID = @OwnerID AND Slot = @Slot;

        IF @@ROWCOUNT = 0
        BEGIN
            ROLLBACK TRANSACTION;
            SELECT -5;
            RETURN;
        END

        -- Rent-Info
        IF EXISTS (SELECT 1 FROM _RentItemInfo WHERE nItemDBID = @ItemToDel)
        BEGIN
            DELETE FROM _RentItemInfo WHERE nItemDBID = @ItemToDel;
        END

        -- Item freigeben
        DECLARE @Rvalue INT;
        EXEC @Rvalue = _STRG_FREE_ITEM_NoTX @ItemToDel;
        IF @Rvalue < 0
        BEGIN
            ROLLBACK TRANSACTION;
            SELECT -6;
            RETURN;
        END

        COMMIT TRANSACTION;
        SELECT 1;
    END TRY
    BEGIN CATCH
        IF @@TRANCOUNT > 0
            ROLLBACK TRANSACTION;
        SELECT -7;
    END CATCH
END