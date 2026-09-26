-- Non-destructive migration for existing installations.
ALTER TABLE utilisateur MODIFY role ENUM('admin','IT','company') NOT NULL DEFAULT 'company';
CREATE TABLE IF NOT EXISTS google_identity (
    subject VARCHAR(255) CHARACTER SET ascii COLLATE ascii_bin PRIMARY KEY,
    account_type ENUM('candidat','company','admin','IT') NOT NULL,
    account_id BIGINT UNSIGNED NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY google_account (account_type, account_id)
) ENGINE=InnoDB;
