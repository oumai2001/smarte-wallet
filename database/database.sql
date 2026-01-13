-- Active: 1768566066660@@127.0.0.1@5432@smarte_walet
CREATE DATABASE smarte_walet;
-- Supprimer les tables si elles existent
DROP TABLE IF EXISTS expenses CASCADE;
DROP TABLE IF EXISTS incomes CASCADE;
DROP TABLE IF EXISTS categories CASCADE;
DROP TABLE IF EXISTS users CASCADE;

-- TABLE: users
CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    is_verified SMALLINT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Index pour améliorer les performances
CREATE INDEX idx_users_email ON users(email);

-- TABLE: categories
CREATE TABLE categories (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    type VARCHAR(10) NOT NULL CHECK (type IN ('income', 'expense')),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Index pour les recherches par type
CREATE INDEX idx_categories_type ON categories(type);

-- TABLE: incomes
CREATE TABLE incomes (
    id SERIAL PRIMARY KEY,
    description VARCHAR(255) NOT NULL,
    amount NUMERIC(10, 2) NOT NULL CHECK (amount > 0),
    income_date DATE NOT NULL,
    category_id INTEGER REFERENCES categories(id) ON DELETE SET NULL,
    user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Index pour améliorer les performances
CREATE INDEX idx_incomes_user_id ON incomes(user_id);
CREATE INDEX idx_incomes_category_id ON incomes(category_id);
CREATE INDEX idx_incomes_date ON incomes(income_date);

-- TABLE: expenses
CREATE TABLE expenses (
    id SERIAL PRIMARY KEY,
    description VARCHAR(255) NOT NULL,
    amount NUMERIC(10, 2) NOT NULL CHECK (amount > 0),
    expense_date DATE NOT NULL,
    category_id INTEGER REFERENCES categories(id) ON DELETE SET NULL,
    user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Index pour améliorer les performances
CREATE INDEX idx_expenses_user_id ON expenses(user_id);
CREATE INDEX idx_expenses_category_id ON expenses(category_id);
CREATE INDEX idx_expenses_date ON expenses(expense_date);

-- FONCTION: Mise à jour automatique de updated_at
CREATE OR REPLACE FUNCTION update_updated_at_column()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = CURRENT_TIMESTAMP;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

-- Triggers pour mise à jour automatique
CREATE TRIGGER update_users_updated_at
    BEFORE UPDATE ON users
    FOR EACH ROW
    EXECUTE FUNCTION update_updated_at_column();

CREATE TRIGGER update_incomes_updated_at
    BEFORE UPDATE ON incomes
    FOR EACH ROW
    EXECUTE FUNCTION update_updated_at_column();

CREATE TRIGGER update_expenses_updated_at
    BEFORE UPDATE ON expenses
    FOR EACH ROW
    EXECUTE FUNCTION update_updated_at_column();

-- DONNÉES INITIALES: Catégories

-- Catégories de revenus
INSERT INTO categories (name, type) VALUES
('Salaire', 'income'),
('Freelance', 'income'),
('Investissement', 'income'),
('Prime', 'income'),
('Location', 'income'),
('Autre revenu', 'income');

-- Catégories de dépenses
INSERT INTO categories (name, type) VALUES
('Alimentation', 'expense'),
('Transport', 'expense'),
('Logement', 'expense'),
('Loisirs', 'expense'),
('Santé', 'expense'),
('Éducation', 'expense'),
('Vêtements', 'expense'),
('Assurances', 'expense'),
('Téléphone & Internet', 'expense'),
('Services publics', 'expense'),
('Autre dépense', 'expense');

-- UTILISATEUR DE TEST 
-- Mot de passe: test123 (hashé avec password_hash)
INSERT INTO users (full_name, email, password, is_verified) VALUES
('Ahmed Bennani', 'test@smartewallet.ma', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1);

-- DONNÉES DE TEST 
-- Revenus de test pour l'utilisateur 1
INSERT INTO incomes (description, amount, income_date, category_id, user_id) VALUES
('Salaire janvier 2025', 15000.00, '2025-01-05', 1, 1),
('Projet freelance', 5000.00, '2025-01-15', 2, 1),
('Prime performance', 3000.00, '2025-01-20', 4, 1);

-- Dépenses de test pour l'utilisateur 1
INSERT INTO expenses (description, amount, expense_date, category_id, user_id) VALUES
('Courses Marjane', 1200.00, '2025-01-10', 7, 1),
('Carburant', 800.00, '2025-01-12', 8, 1),
('Loyer appartement', 4500.00, '2025-01-01', 9, 1),
('Netflix & Spotify', 150.00, '2025-01-05', 10, 1),
('Restaurant', 450.00, '2025-01-18', 10, 1);

-- VUES UTILES

-- Vue: Statistiques par utilisateur
CREATE OR REPLACE VIEW user_statistics AS
SELECT 
    u.id as user_id,
    u.full_name,
    COALESCE(SUM(i.amount), 0) as total_income,
    COALESCE(SUM(e.amount), 0) as total_expense,
    COALESCE(SUM(i.amount), 0) - COALESCE(SUM(e.amount), 0) as balance,
    COUNT(DISTINCT i.id) as income_count,
    COUNT(DISTINCT e.id) as expense_count
FROM users u
LEFT JOIN incomes i ON u.id = i.user_id
LEFT JOIN expenses e ON u.id = e.user_id
GROUP BY u.id, u.full_name;

-- Vue: Dépenses par catégorie
CREATE OR REPLACE VIEW expenses_by_category AS
SELECT 
    c.name as category_name,
    c.type,
    e.user_id,
    COUNT(e.id) as transaction_count,
    SUM(e.amount) as total_amount,
    AVG(e.amount) as average_amount
FROM categories c
LEFT JOIN expenses e ON c.id = e.category_id
GROUP BY c.id, c.name, c.type, e.user_id;

-- Vue: Revenus par catégorie
CREATE OR REPLACE VIEW incomes_by_category AS
SELECT 
    c.name as category_name,
    c.type,
    i.user_id,
    COUNT(i.id) as transaction_count,
    SUM(i.amount) as total_amount,
    AVG(i.amount) as average_amount
FROM categories c
LEFT JOIN incomes i ON c.id = i.category_id
GROUP BY c.id, c.name, c.type, i.user_id;

-- FONCTIONS UTILITAIRES

-- Fonction: Calculer le solde d'un utilisateur
CREATE OR REPLACE FUNCTION get_user_balance(p_user_id INTEGER)
RETURNS NUMERIC AS $$
DECLARE
    v_balance NUMERIC;
BEGIN
    SELECT 
        COALESCE(SUM(i.amount), 0) - COALESCE(SUM(e.amount), 0)
    INTO v_balance
    FROM users u
    LEFT JOIN incomes i ON u.id = i.user_id
    LEFT JOIN expenses e ON u.id = e.user_id
    WHERE u.id = p_user_id;
    
    RETURN v_balance;
END;
$$ LANGUAGE plpgsql;

-- Fonction: Statistiques mensuelles d'un utilisateur
CREATE OR REPLACE FUNCTION get_monthly_stats(
    p_user_id INTEGER,
    p_year INTEGER,
    p_month INTEGER
)
RETURNS TABLE(
    total_income NUMERIC,
    total_expense NUMERIC,
    balance NUMERIC,
    income_count BIGINT,
    expense_count BIGINT
) AS $$
BEGIN
    RETURN QUERY
    SELECT 
        COALESCE(SUM(i.amount), 0) as total_income,
        COALESCE(SUM(e.amount), 0) as total_expense,
        COALESCE(SUM(i.amount), 0) - COALESCE(SUM(e.amount), 0) as balance,
        COUNT(DISTINCT i.id) as income_count,
        COUNT(DISTINCT e.id) as expense_count
    FROM users u
    LEFT JOIN incomes i ON u.id = i.user_id 
        AND EXTRACT(YEAR FROM i.income_date) = p_year
        AND EXTRACT(MONTH FROM i.income_date) = p_month
    LEFT JOIN expenses e ON u.id = e.user_id
        AND EXTRACT(YEAR FROM e.expense_date) = p_year
        AND EXTRACT(MONTH FROM e.expense_date) = p_month
    WHERE u.id = p_user_id;
END;
$$ LANGUAGE plpgsql;

-- GRANTS (Permissions)
-- Ajustez selon votre utilisateur PostgreSQL
-- GRANT ALL PRIVILEGES ON ALL TABLES IN SCHEMA public TO your_user;
-- GRANT ALL PRIVILEGES ON ALL SEQUENCES IN SCHEMA public TO your_user;

-- VÉRIFICATIONS

-- Afficher toutes les tables
SELECT tablename FROM pg_tables WHERE schemaname = 'public';

-- Compter les catégories
SELECT type, COUNT(*) FROM categories GROUP BY type;

-- Afficher les utilisateurs
SELECT id, full_name, email, created_at FROM users;

/*
1. PostgreSQL vs MySQL:
   - SERIAL au lieu de AUTO_INCREMENT
   - SMALLINT au lieu de TINYINT
   - VARCHAR(10) pour type (pas d'ENUM natif)
   - Fonctions et triggers en PL/pgSQL

2. Performance:
   - Index créés sur les clés étrangères
   - Index sur les dates pour les filtres
   - Vues pour les requêtes fréquentes

3. Sécurité:
   - Contraintes CHECK sur les montants (> 0)
   - ON DELETE CASCADE pour les données utilisateur
   - ON DELETE SET NULL pour les catégories

4. Migration depuis MySQL:
   - Remplacer AUTO_INCREMENT par SERIAL
   - Adapter les types de données
   - Remplacer NOW() par CURRENT_TIMESTAMP
   - Adapter la syntaxe des fonctions
*/