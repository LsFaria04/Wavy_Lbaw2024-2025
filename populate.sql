INSERT INTO Users (userName, passwordHash, bio, email, state, visibilityPublic, isAdmin) 
VALUES 
    ('Admin', 'hash1', 'Community leader passionate about making a difference.', 'administrator@example.com', 'active', FALSE, TRUE),
    ('BobJohnson', 'hash2', 'Avid hiker and nature enthusiast, always seeking the next adventure.', 'bob.johnson@example.com', 'active', TRUE, FALSE),
    ('CharlieBrown', 'hash3', 'Art lover who enjoys visiting galleries and creating new pieces.', 'charlie.brown@example.com', 'suspended', TRUE, FALSE),
    ('DanaWhite', 'hash4', 'Foodie and chef, always experimenting with new recipes.', 'dana.white@example.com', 'active', TRUE, FALSE),
    ('EveBlack', 'hash5', 'Traveler at heart, exploring cultures and cuisines around the world.', 'eve.black@example.com', 'deleted', TRUE, FALSE),
    ('FrankMoore', 'hash6', 'Sports coach who enjoys mentoring young athletes and staying active.', 'frank.moore@example.com', 'active', TRUE, FALSE),
    ('GraceHall', 'hash7', 'Yoga instructor and wellness advocate, passionate about mindfulness.', 'grace.hall@example.com', 'active', TRUE, FALSE),
    ('HenryKing', 'hash8', 'Gardener with a love for growing fresh vegetables and herbs.', 'henry.king@example.com', 'suspended', TRUE, FALSE),
    ('IvyAdams', 'hash9', 'Bookworm and aspiring novelist, always lost in a good story.', 'ivy.adams@example.com', 'active', TRUE, FALSE),
    ('JackLee', 'hash10', 'Music enthusiast and amateur guitarist, loves performing at local cafes.', 'jack.lee@example.com', 'active', FALSE, FALSE);

INSERT INTO GROUPS (groupName, description, visibilityPublic, ownerID) 
VALUES 
    ('Adventure Seekers', 'A group for those who love outdoor adventures and exploring new places.', TRUE, 2),
    ('Art Enthusiasts', 'A community for art lovers to share and discuss their favorite pieces.', TRUE, 3),
    ('Culinary Explorers', 'For food lovers who enjoy cooking and trying new recipes.', TRUE, 4),
    ('Travel Buddies', 'Connect with fellow travelers to share tips and experiences.', TRUE, 5),
    ('Sports Betting', 'Join our group to lose all your money.', FALSE, 6),
    ('Wellness and Yoga', 'A space for yoga practitioners to connect and share their journeys.', TRUE, 7),
    ('Garden Lovers', 'For gardening enthusiasts to share tips and grow together.', TRUE, 8),
    ('Book Club', 'A cozy club for book lovers to discuss and recommend their favorite reads.', TRUE, 9),
    ('Music Jam', 'A group for musicians to come together, jam, and share their music.', TRUE, 10),
    ('Tech Innovators', 'A community for tech enthusiasts to discuss the latest trends and innovations.', TRUE, 1);

INSERT INTO GROUP_MEMBERSHIP (groupID, userID) 
VALUES 
    (1, 2), 
    (1, 4), 
    (2, 3), 
    (3, 4), 
    (4, 5), 
    (5, 6), 
    (6, 7), 
    (7, 8), 
    (8, 9), 
    (9, 10),
    (10,1), 
    (1, 3), 
    (2, 6); 

INSERT INTO JOIN_GROUP_REQUEST (groupID, userID, date, state) 
VALUES 
    (2, 5, NOW(), 'Pending'),  
    (3, 7, NOW(), 'Rejected'), 
    (4, 2, NOW(), 'Pending'),    
    (5, 1, NOW(), 'Accepted'),   
    (6, 8, NOW(), 'Pending'),    
    (7, 4, NOW(), 'Rejected');    

INSERT INTO MESSAGE (receiverID, senderID, message, date) 
VALUES 
    (2, 1, 'Welcome to the community, Bob!', '2024-11-04 10:00:00'),
    (3, 1, 'Hi Charlie! Let’s collaborate on a project.', '2024-11-04 10:15:00'),
    (4, 1, 'Dana, have you tried the new Italian restaurant?', '2024-11-04 10:30:00'),
    (5, 2, 'Eve, your portfolio is impressive!', '2024-11-04 10:45:00'),
    (6, 3, 'Hey Frank! Interested in joining my hiking group?', '2024-11-04 11:00:00'),
    (7, 4, 'Grace, I loved your recent yoga class!', '2024-11-04 11:15:00'),
    (8, 5, 'Henry, do you have any tips for starting a garden?', '2024-11-04 11:30:00'),
    (9, 6, 'Ivy, I think your story would make a great film!', '2024-11-04 11:45:00'),
    (10, 7, 'Jack, I’d love to hear you play sometime!', '2024-11-04 12:00:00'),
    (1, 8, 'Thanks for your feedback on my last project, Admin!', '2024-11-04 12:15:00');

INSERT INTO POST (userID, message, visibilityPublic, createdDate, groupID) 
VALUES 
    (2, 'Just returned from an amazing hike in the mountains.', TRUE, '2022-10-01 10:30:00', NULL), 
    (3, 'Here’s my latest painting, inspired by nature!', TRUE, '2024-10-03 14:00:00', NULL),
    (4, 'Check out this delicious recipe I just made!', TRUE, '2023-10-05 09:15:00', NULL),
    (5, 'Just visited a new country, and it was incredible!', FALSE, '2017-10-08 18:45:00', NULL),
    (6, 'Had a great day coaching my team today!', TRUE, '2024-10-10 12:20:00', NULL), 
    (7, 'Practicing my yoga poses today, feeling great!', TRUE, '2024-10-12 07:30:00', NULL),
    (8, 'Can’t wait for my vegetables to be ready for harvest!', TRUE, '2024-10-15 16:40:00', 7), 
    (9, 'Currently working on my novel, it’s coming together!', TRUE, '2024-10-18 20:05:00', NULL), 
    (10, 'Played at a local café last night, had a blast!', TRUE, '2024-10-20 21:15:00', NULL);  

INSERT INTO TOPIC (topicName) 
VALUES 
    ('Technology'),
    ('Health'),
    ('Travel'),
    ('Food'),
    ('Art'),
    ('Sports'),
    ('Environment'),
    ('Gaming'),
    ('Gambling'),
    ('Politics'),
    ('Education');

INSERT INTO COMMENT (userID, message, createdDate, postID, parentCommentID) 
VALUES 
    (3, 'Your painting is amazing! What inspired you?', '2024-01-01 15:00:00', 3, NULL),
    (4, 'I need to try that recipe!', '2024-10-04 15:00:00', 4, NULL),
    (3, 'Can’t wait to see the finished product!', NOW(), 1, NULL),
    (2, 'I loved the view from the top of the mountain!', '2024-10-04 15:01:00', NULL, 2);

INSERT INTO LIKES (userID, createdDate, postID, commentID) 
VALUES 
    (2, NOW(), 1, NULL),   
    (3, NOW(), NULL, 1),  
    (4, NOW(), 2, NULL),  
    (5, NOW(), NULL, 2),  
    (6, NOW(), 3, NULL),   
    (7, NOW(), NULL, 3),   
    (8, NOW(), 1, NULL),  
    (9, NOW(), 3, NULL), 
    (10, NOW(), NULL, 2);

INSERT INTO MEDIA (path, postID, commentID, userID) VALUES
    ('/media/images/photo1.jpg', 1, NULL, NULL),
    ('/media/images/photo2.jpg', NULL, 2, NULL),
    ('/media/videos/video1.mp4', 3, NULL, NULL),
    ('/media/images/profile1.jpg', NULL, NULL, 1),  
    ('/media/audio/audio1.mp3', NULL, NULL, 2);    

INSERT INTO FOLLOW (followerID, followeeID, state, followDate) VALUES
    (1, 2, 'Accepted', NOW()),
    (2, 10, 'Pending', NOW()),
    (4, 5, 'Accepted', NOW()),
    (5, 1, 'Rejected', NOW()),
    (6, 7, 'Accepted', NOW());

INSERT INTO NOTIFICATION (receiverID, date, seen, followID, commentID, likeID) VALUES
    (1, NOW(), FALSE, 2, NULL, NULL),
    (2, NOW(), TRUE, 1, NULL, NULL), 
    (3, NOW(), FALSE, NULL, 1, NULL),
    (4, NOW(), TRUE, NULL, NULL, 1), 
    (5, NOW(), FALSE, NULL, NULL, 2);

INSERT INTO BLOCK (blockerID, blockedID) VALUES
    (1, 2), 
    (3, 1), 
    (4, 5); 

INSERT INTO USER_TOPICS (userID, topicID) 
VALUES 
    (1, 2), 
    (1, 3), 
    (2, 1), 
    (2, 4), 
    (3, 2),   
    (4, 1),   
    (4, 3), 
    (5, 4), 
    (6, 2),   
    (7, 3);   

INSERT INTO USER_REPORTS (userID, postID, commentID, reason) VALUES
    (1, 1, NULL, 'Inappropriate content'),
    (2, NULL, 1, 'Spam'),                 
    (3, 3, NULL, 'Hate speech'),          
    (4, NULL, 3, 'Offensive language');   

INSERT INTO GROUP_INVITATION (groupID, userID, date, state) VALUES
    (3, 2, NOW(), 'Pending'), 
    (8, 3, NOW(), 'Accepted'),
    (2, 1, NOW(), 'Rejected');

INSERT INTO POST_TOPICS (postID, topicID) VALUES
    (1, 1), 
    (1, 2),
    (2, 3), 
    (3, 1); 