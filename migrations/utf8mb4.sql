-- Converts click's Pages table from latin1 to utf8mb4, so posts can hold emoji
-- and any other unicode character. Run it once on a database imported from an
-- older server, see README.md. Running it again changes nothing.
--
-- Click always sent its text as UTF-8 over a latin1 connection, so the latin1
-- columns already hold UTF-8 bytes. Converting them straight to utf8mb4 would
-- treat each byte as a latin1 character and garble every non-ASCII letter, so
-- the text columns pass through binary types, which keep the bytes as they are.
--
-- The table moves to InnoDB too. MyISAM can't index Page as utf8mb4, which
-- needs 1020 bytes of its 1000 byte limit, and InnoDB survives crashes and
-- dumps consistently while click is running.

-- Stops before changing anything if a post isn't valid UTF-8, which the
-- conversion would refuse. To list those posts:
--
--   SELECT `ID`, `Page` FROM `Pages`
--   WHERE NOT (CAST(CONVERT(CAST(`Page` AS BINARY) USING utf8mb4) AS BINARY) <=> CAST(`Page` AS BINARY))
--      OR NOT (CAST(CONVERT(CAST(`Data` AS BINARY) USING utf8mb4) AS BINARY) <=> CAST(`Data` AS BINARY));
DELIMITER //
BEGIN NOT ATOMIC
    IF EXISTS (
        SELECT 1 FROM `Pages`
        WHERE NOT (CAST(CONVERT(CAST(`Page` AS BINARY) USING utf8mb4) AS BINARY) <=> CAST(`Page` AS BINARY))
           OR NOT (CAST(CONVERT(CAST(`Data` AS BINARY) USING utf8mb4) AS BINARY) <=> CAST(`Data` AS BINARY))
    ) THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Some posts are not valid UTF-8, so nothing was changed. migrations/utf8mb4.sql shows how to list them.';
    END IF;
END//
DELIMITER ;

-- First binary types, which keep the stored bytes as they are. Page has room
-- for 255 utf8mb4 characters of 4 bytes, so running this again cuts nothing.
ALTER TABLE `Pages`
    ENGINE = InnoDB,
    MODIFY `Page` VARBINARY(1020) NOT NULL,
    MODIFY `Data` LONGBLOB NOT NULL;

-- Then the same bytes read as utf8mb4. Page names stay case-insensitive as
-- before, but accented letters and emoji are told apart. IP gets room for
-- IPv6 addresses, which the old 32 characters cut short.
ALTER TABLE `Pages`
    DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_uca1400_as_ci,
    MODIFY `Page` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_uca1400_as_ci NOT NULL,
    MODIFY `IP` VARCHAR(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_uca1400_as_ci NOT NULL,
    MODIFY `Data` LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_uca1400_as_ci NOT NULL;
