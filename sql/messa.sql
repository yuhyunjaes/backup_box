CREATE TABLE messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    room_id INT NOT NULL,
    sender_id TEXT NOT NULL,
    message TEXT NOT NULL,
    sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

SELECT DISTINCT r.user1_id, r.user2_id 
FROM chat_rooms r 
JOIN messages m ON r.id = m.room_id 
WHERE r.user1_id = 'yuhyunjae123' OR r.user2_id = 'yuhyunjae123'
ORDER BY m.sent_at DESC