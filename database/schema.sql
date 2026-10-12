CREATE TABLE membership_plans (
    id INT AUTO_INCREMENT PRIMARY KEY,
    plan_name VARCHAR(100) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    duration_months INT NOT NULL,
    description TEXT
);

INSERT INTO membership_plans (plan_name, price, duration_months, description) VALUES
    ('1 Month', 700, 1, 'Gym access for one month.'),
    ('3 Months', 1500, 3, 'Three months of gym access.'),
    ('6 Months', 2500, 6, 'Six months of gym access.'),
    ('1 Year', 4500, 12, 'Twelve months of gym access.');

CREATE TABLE members (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone VARCHAR(15) NOT NULL,
    membership_plan_id INT,
    join_date DATE NOT NULL,
    FOREIGN KEY (membership_plan_id) REFERENCES membership_plans(id)
);

CREATE TABLE payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    member_id INT,
    amount DECIMAL(10, 2) NOT NULL,
    payment_date DATE NOT NULL,
    FOREIGN KEY (member_id) REFERENCES members(id)
);

CREATE TABLE IF NOT EXISTS machines (
    id INT AUTO_INCREMENT PRIMARY KEY,
    machine_name VARCHAR(100) NOT NULL UNIQUE,
    category VARCHAR(50) NOT NULL,
    total_units INT NOT NULL DEFAULT 1,
    available_units INT NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);