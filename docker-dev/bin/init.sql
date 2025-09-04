INSERT INTO `user`
VALUES (1, '2025-07-27 16:41:23', '2025-08-19 08:29:16', 'admin', '[\"ROLE_SUPERUSER\"]', '$2y$13$uzanAyUB3SZVPd.Z8jZeJeWcSjC5pUpAb2rw7Wyf/ChElu2WpSX4e',
        'System', 'Administrator', NULL, NULL, 'office@most-connect.com', '+436643992254', NULL, NULL, 1, '3594138-68a0e3f57ca57.jpg', NULL, NULL);

INSERT INTO `email_template`
VALUES (1, '2025-09-03 18:37:11', '2025-09-03 18:58:10', 'password_reset_request', 'de',
        'Passwort zurücksetzen',
        '<p>Hallo {{ user.firstName ?? user.email }},</p>\r\n\r\n<p>für dein Konto bei MC Base wurde eine Zurücksetzung des Passworts angefordert.</p>\r\n\r\n<p style=\"margin:16px 0;\">\r\n  <a href=\"{{ resetUrl }}\"\r\n     style=\"display:inline-block;padding:12px 18px;text-decoration:none;border-radius:6px;border:1px solid #ddd;\">\r\n    Passwort jetzt zurücksetzen\r\n  </a>\r\n</p>\r\n\r\n<p>Der Link ist bis <strong>{{ expiresAt|date(\'d.m.Y H:i\') }}</strong> gültig. Danach kannst du jederzeit einen neuen Link anfordern.</p>\r\n\r\n<p>Falls du diese Anfrage nicht gestellt hast, ignoriere diese E-Mail einfach oder kontaktiere unseren Support.</p>\r\n\r\n<hr style=\"border:none;border-top:1px solid #eee;margin:16px 0;\">\r\n<p style=\"font-size:12px;color:#666;\">\r\n  most-connect.com • Support: <a href=\"mailto:office@most-connect.com\">office@most-connect.com</a><br>\r\n  Dies ist eine automatische Nachricht. Antworten auf diese E-Mail werden nicht gelesen.\r\n</p>',
        'Hallo {{ user.firstName ?? user.email }},\r\n\r\nfür dein Konto bei MC Base wurde eine Zurücksetzung des Passworts angefordert.\r\n\r\nPasswort jetzt zurücksetzen:\r\n{{ resetUrl }}\r\n\r\nDer Link ist bis {{ expiresAt|date(\'d.m.Y H:i\') }} gültig. Danach kannst du jederzeit einen neuen Link anfordern.\r\n\r\nWenn du diese Anfrage nicht gestellt hast, ignoriere diese E-Mail oder kontaktiere unseren Support: office@most-connect.com\r\n\r\n— most-connect.com\r\n(Auto-Mail, bitte nicht antworten)',
        NULL);
