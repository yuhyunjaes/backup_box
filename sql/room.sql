CREATE TABLE chat_rooms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user1_id TEXT NOT NULL,
    user2_id TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(user1_id, user2_id)
);

SELECT r.id, r.user1_id, r.user2_id, MAX(m.sent_at) AS last_message_time
FROM chat_rooms r
LEFT JOIN messages m ON r.id = m.room_id
WHERE r.user1_id = 'a5347709' OR r.user2_id = 'a5347709'
GROUP BY r.id
ORDER BY last_message_time DESC