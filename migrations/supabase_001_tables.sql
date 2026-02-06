-- Logik Mastermind – tabulky pro Supabase (PostgreSQL)
-- Spusť v Supabase Dashboard → SQL Editor.

-- Výsledky her
CREATE TABLE IF NOT EXISTS results (
  id BIGSERIAL PRIMARY KEY,
  player_name TEXT NOT NULL,
  mode TEXT NOT NULL DEFAULT '1p',
  won BOOLEAN NOT NULL DEFAULT false,
  attempts SMALLINT NOT NULL,
  difficulty TEXT,
  game_code TEXT,
  created_at TIMESTAMPTZ NOT NULL DEFAULT now()
);

CREATE INDEX IF NOT EXISTS idx_results_player ON results (player_name);
CREATE INDEX IF NOT EXISTS idx_results_created ON results (created_at DESC);

-- Online hry
CREATE TABLE IF NOT EXISTS games (
  id BIGSERIAL PRIMARY KEY,
  game_code TEXT NOT NULL UNIQUE,
  player1_name TEXT NOT NULL,
  player2_name TEXT,
  secret JSONB,
  status TEXT NOT NULL DEFAULT 'waiting' CHECK (status IN ('waiting','secret_entered','playing','won','lost')),
  history JSONB,
  max_attempts SMALLINT NOT NULL DEFAULT 10,
  created_at TIMESTAMPTZ NOT NULL DEFAULT now(),
  updated_at TIMESTAMPTZ NOT NULL DEFAULT now()
);

CREATE INDEX IF NOT EXISTS idx_games_code ON games (game_code);
CREATE INDEX IF NOT EXISTS idx_games_status ON games (status);
CREATE INDEX IF NOT EXISTS idx_games_updated ON games (updated_at DESC);

-- Trigger pro updated_at
CREATE OR REPLACE FUNCTION set_updated_at()
RETURNS TRIGGER AS $$
BEGIN
  NEW.updated_at = now();
  RETURN NEW;
END;
$$ LANGUAGE plpgsql;

DROP TRIGGER IF EXISTS games_updated_at ON games;
CREATE TRIGGER games_updated_at
  BEFORE UPDATE ON games
  FOR EACH ROW EXECUTE FUNCTION set_updated_at();

-- RLS: povolit anon přístup pro veřejnou hru (čtení + zápis)
ALTER TABLE results ENABLE ROW LEVEL SECURITY;
ALTER TABLE games ENABLE ROW LEVEL SECURITY;

DROP POLICY IF EXISTS "results_anon_all" ON results;
CREATE POLICY "results_anon_all" ON results FOR ALL TO anon USING (true) WITH CHECK (true);

DROP POLICY IF EXISTS "games_anon_all" ON games;
CREATE POLICY "games_anon_all" ON games FOR ALL TO anon USING (true) WITH CHECK (true);
