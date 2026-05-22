INSERT INTO dispatch_statuses (dispatch_status, color, created_at, updated_at)
VALUES
    ('ACTIVATED', '#27d5e1', NOW(), NOW()),
    ('CANCELLED', '#b71c1c', NOW(), NOW()),
    ('FOR INSTALLATION', '#4a148c', NOW(), NOW()),
    ('RE-SCHEDULE', '#beb5b0', NOW(), NOW()),
    ('ON-HOLD INSTALLATION', '#ef6c00', NOW(), NOW()),
    ('FOR MSA INSTALLATION', '#ec407a', NOW(), NOW()),
    ('FOR ACTIVATION', '#00bcd4', NOW(), NOW()),
    ('RESCHED BY MSA', '#ffeb3b', NOW(), NOW()),
    ('JRU UNINSTALLABLE', '#6fe6fc', NOW(), NOW()),
    ('DOUBLE ENTRY', '#1565c0', NOW(), NOW())
ON DUPLICATE KEY UPDATE
    color = VALUES(color),
    updated_at = VALUES(updated_at);
