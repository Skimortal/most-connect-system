INSERT INTO user (
    company_id, username, roles, password, first_name, last_name,
    department, position, email, phone_number, mobile_number, fax_number,
    is_active, created_at, updated_at
) VALUES (
             NULL,
             'admin',
             '[\"ROLE_SUPERUSER\"]',
             '\$2y\$13\$SyPxQI1B4D75Kq5x4UN3DedK/YgpMeHsHl3GnOVPWzwjmJiFCP9Xe',
             'System', 'Administrator',
             NULL, NULL,
             'office@most-connect.com',
             NULL, NULL, NULL,
             1,
             NOW(), NOW()
         );