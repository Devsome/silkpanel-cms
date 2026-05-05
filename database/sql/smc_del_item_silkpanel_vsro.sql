CREATE OR ALTER PROCEDURE [dbo].[_SMC_DEL_ITEM_SILKPANEL_VSRO]
    @TargetStorage  INT,            -- 0=Inventory, 1=Chest, 2=GuildChest, 3=AvatarInventory
    @OwnerName      VARCHAR(128),   -- Charaktername (bzw. Guildname bei 2)
    @Slot           INT
AS
BEGIN
    SET NOCOUNT ON;
    SET XACT_ABORT ON;

    -- 1. Gültigen Storage-Typ prüfen
    IF @TargetStorage NOT IN (0, 1, 2, 3)
    BEGIN
        SELECT -1;
        RETURN;
    END

    DECLARE @OwnerID INT = NULL;
    DECLARE @ItemToDel BIGINT = NULL;

    -- 2. Owner-ID und ItemID abhängig vom Storage ermitteln
    IF @TargetStorage = 0             -- Inventar
    BEGIN
        SELECT @OwnerID = CharID FROM _Char WHERE CharName16 = @OwnerName;
        IF @@ROWCOUNT = 0 OR @OwnerID IS NULL BEGIN SELECT -2; RETURN; END

        SELECT @ItemToDel = ItemID FROM _Inventory WHERE CharID = @OwnerID AND Slot = @Slot;
        IF @@ROWCOUNT = 0 OR @ItemToDel = 0 BEGIN SELECT -3; RETURN; END
    END
    ELSE IF @TargetStorage = 1        -- Chest (Storage)
    BEGIN
        SELECT @OwnerID = JID FROM _AccountJID WHERE AccountID = @OwnerName;
        IF @@ROWCOUNT = 0 OR @OwnerID IS NULL BEGIN SELECT -2; RETURN; END

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

    -- 3. Sicherheitscheck: Keine Item-ID
    IF @ItemToDel IS NULL OR @ItemToDel = 0
    BEGIN
        SELECT -4;
        RETURN;
    END

    -- 4. Transaktion: Slot leeren, Rent entfernen, Item freigeben
    BEGIN TRY
        BEGIN TRANSACTION;

        IF @TargetStorage = 0
            UPDATE _Inventory SET ItemID = 0 WHERE CharID = @OwnerID AND Slot = @Slot;
        ELSE IF @TargetStorage = 1
            UPDATE _Chest SET ItemID = 0 WHERE UserJID = @OwnerID AND Slot = @Slot;
        ELSE IF @TargetStorage = 2
            UPDATE _GuildChest SET ItemID = 0 WHERE GuildID = @OwnerID AND Slot = @Slot;
        ELSE
            UPDATE _InventoryForAvatar SET ItemID = 0 WHERE CharID = @OwnerID AND Slot = @Slot;

        IF @@ROWCOUNT = 0
        BEGIN
            ROLLBACK TRANSACTION;
            SELECT -5;
            RETURN;
        END

        -- Ggfs. Rent-Info löschen
        IF EXISTS (SELECT 1 FROM _RentItemInfo WHERE nItemDBID = @ItemToDel)
        BEGIN
            DELETE FROM _RentItemInfo WHERE nItemDBID = @ItemToDel;
        END

        -- Item tatsächlich freigeben
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
        SELECT -7;  -- Unerwarteter Fehler
    END CATCH
END