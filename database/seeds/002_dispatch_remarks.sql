INSERT INTO dispatch_remarks (dispatch_remarks, created_at, updated_at)
VALUES
    ('WRONG AREA', NOW(), NOW()),
    ('WITH EXISTING LINE', NOW(), NOW()),
    ('UNDECIDED', NOW(), NOW()),
    ('UNCONTACTED', NOW(), NOW()),
    ('SUBS UNKNOWN', NOW(), NOW()),
    ('SITE CONCERN', NOW(), NOW()),
    ('PERMIT ISSUE', NOW(), NOW()),
    ('PASSING PRIVATE PROPERTY', NOW(), NOW()),
    ('PASSING BUSY ROAD', NOW(), NOW()),
    ('OVERSPAN', NOW(), NOW())
ON DUPLICATE KEY UPDATE
    updated_at = VALUES(updated_at);
