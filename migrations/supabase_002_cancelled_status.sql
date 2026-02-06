-- Přidat status 'cancelled' pro ukončení hry zadavatelem
ALTER TABLE games DROP CONSTRAINT IF EXISTS games_status_check;
ALTER TABLE games ADD CONSTRAINT games_status_check
  CHECK (status IN ('waiting','secret_entered','playing','won','lost','cancelled'));
