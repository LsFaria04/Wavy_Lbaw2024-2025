
INSERT INTO Users (userName, passwordHash, bio, email, state, visibilityPublic, isAdmin) 
VALUES 
    ('Admin', '$2y$10$ucilw0DqyGGYCybQLesgLOCCZLy07yOwMcdLMrU50yYPTTxLMg37C', 'Community leader passionate about making a difference.', 'administrator@example.com', 'active', FALSE, TRUE),
    ('BobJohnson', '$2y$10$ucilw0DqyGGYCybQLesgLOCCZLy07yOwMcdLMrU50yYPTTxLMg37C', 'Avid hiker and nature enthusiast, always seeking the next adventure.', 'bob.johnson@example.com', 'active', TRUE, FALSE),
    ('CharlieBrown', '$2y$10$ucilw0DqyGGYCybQLesgLOCCZLy07yOwMcdLMrU50yYPTTxLMg37C', 'Art lover who enjoys visiting galleries and creating new pieces.', 'charlie.brown@example.com', 'suspended', TRUE, FALSE),
    ('DanaWhite', '$2y$10$ucilw0DqyGGYCybQLesgLOCCZLy07yOwMcdLMrU50yYPTTxLMg37C', 'Foodie and chef, always experimenting with new recipes.', 'dana.white@example.com', 'active', TRUE, FALSE),
    ('EveBlack', '$2y$10$ucilw0DqyGGYCybQLesgLOCCZLy07yOwMcdLMrU50yYPTTxLMg37C', 'Traveler at heart, exploring cultures and cuisines around the world.', 'eve.black@example.com', 'active', TRUE, FALSE),
    ('FrankMoore', '$2y$10$ucilw0DqyGGYCybQLesgLOCCZLy07yOwMcdLMrU50yYPTTxLMg37C', 'Sports coach who enjoys mentoring young athletes and staying active.', 'frank.moore@example.com', 'active', TRUE, FALSE),
    ('GraceHall', '$2y$10$ucilw0DqyGGYCybQLesgLOCCZLy07yOwMcdLMrU50yYPTTxLMg37C', 'Yoga instructor and wellness advocate, passionate about mindfulness.', 'grace.hall@example.com', 'active', TRUE, FALSE),
    ('HenryKing', '$2y$10$ucilw0DqyGGYCybQLesgLOCCZLy07yOwMcdLMrU50yYPTTxLMg37C', 'Gardener with a love for growing fresh vegetables and herbs.', 'henry.king@example.com', 'suspended', TRUE, FALSE),
    ('IvyAdams', '$2y$10$ucilw0DqyGGYCybQLesgLOCCZLy07yOwMcdLMrU50yYPTTxLMg37C', 'Bookworm and aspiring novelist, always lost in a good story.', 'ivy.adams@example.com', 'active', TRUE, FALSE),
    ('JackLee', '$2y$10$ucilw0DqyGGYCybQLesgLOCCZLy07yOwMcdLMrU50yYPTTxLMg37C', 'Music enthusiast and amateur guitarist, loves performing at local cafes.', 'jack.lee@example.com', 'active', FALSE, FALSE),
    ('Frank', '$2y$10$ucilw0DqyGGYCybQLesgLOCCZLy07yOwMcdLMrU50yYPTTxLMg37C', 'MUFC Fan Account • NOT Affiliated with Rúben Amorim • DM for promos/ads etc', 'frank@example.com', 'active', TRUE, FALSE);

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
    ('Taylor Swift', 'A group for musicians to come together, jam, and share their music.', TRUE, 10),
    ('Manchester United Fans', 'A group for manchester united fans to cry and celebrate together.', TRUE, 11);

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
    (1, 3), 
    (2, 6),
    (10, 11),
    (9, 2),
    (9, 4),
    (9, 5),
    (9, 6),
    (9, 7);


INSERT INTO JOIN_GROUP_REQUEST (groupID, userID, createdDate) 
VALUES 
    (2, 5, '2024-10-03 14:00:00'),  
    (3, 7, '2024-10-03 14:00:00'), 
    (3, 5, '2024-10-03 14:00:00'),
    (3, 6, '2024-10-03 14:00:00'),
    (4, 2, '2024-10-03 14:00:00'),    
    (6, 8, '2024-10-03 14:00:00'),    
    (7, 4, '2024-10-03 14:00:00');    

INSERT INTO MESSAGE (receiverID, senderID, message, date) 
VALUES 
    (5, 2, 'Eve, your portfolio is impressive!', '2024-11-04 10:45:00'),
    (6, 3, 'Hey Frank! Interested in joining my hiking group?', '2024-11-04 11:00:00'),
    (7, 4, 'Grace, I loved your recent yoga class!', '2024-11-04 11:15:00'),
    (8, 5, 'Henry, do you have any tips for starting a garden?', '2024-11-04 11:30:00'),
    (9, 6, 'Ivy, I think your story would make a great film!', '2024-11-04 11:45:00'),
    (10, 7, 'Jack, I’d love to hear you play sometime!', '2024-11-04 12:00:00');

INSERT INTO TOPIC (topicName) 
VALUES 
    ('General'),
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
    ('Education'),
    ('Football'),
    ('Manchester United');

INSERT INTO POST (userID, message, visibilityPublic, createdDate, groupID) 
VALUES 
    (2, 'Just returned from an amazing hike in the mountains.', TRUE, '2022-10-01 10:30:00', NULL), 
    (3, 'Here’s my latest painting, inspired by nature!', TRUE, '2024-10-03 14:00:00', NULL),
    (4, 'Check out this delicious recipe I just made!', TRUE, '2023-10-05 09:15:00', NULL),
    (5, 'Just visited a new country, and it was incredible!', FALSE, '2017-10-08 18:45:00', NULL),
    (6, 'Had a great day coaching my team today!', TRUE, '2024-10-10 12:20:00', NULL),
    (7, 'Petition to make check in at hotels 11am and check out 3pm not over way round wtfffff', TRUE, '2024-10-12 07:30:00', NULL), 
    (8, 'Can’t wait for my vegetables to be ready for harvest!', FALSE, '2024-10-15 16:40:00', 7), 
    (9, 'Currently working on my novel, it’s coming together!', TRUE, '2024-10-18 20:05:00', NULL), 
    (10, 'Played at a local café last night, had a blast!', FALSE, '2024-10-20 21:15:00', NULL),
    (5, 'Just completed a challenging workout—feeling accomplished!', TRUE, '2024-11-01 18:15:00', NULL),
    (2, 'Exploring new music genres to expand my playlist!', TRUE, '2024-01-11 20:45:00', NULL),
    (3, 'What’s everyone’s favorite movie from the 90s?', TRUE, '2024-01-12 22:00:00', NULL),
    (4, 'Volunteered at the local animal shelter today—such a rewarding experience!', TRUE, '2024-01-13 17:30:00', NULL),
    (5, 'Who else is getting excited for the upcoming holiday season?', FALSE, '2024-01-14 12:00:00', NULL),
    (6, 'Working on my coding skills—any recommendations for resources?', TRUE, '2024-01-15 09:45:00', NULL),
    (7, 'Just finished a beautiful sunset painting!', TRUE, '2024-02-16 19:00:00', NULL),
    (8, 'Mewing tournament today at 3pm, do not miss', TRUE, '2024-03-17 14:30:00', NULL),
    (9, 'Had a fantastic time at the local food festival last night!', TRUE, '2024-04-18 20:00:00', NULL),
    (3, 'Looking for tips on traveling solo—any experiences to share?', TRUE, '2024-05-19 15:00:00', NULL),
    (11, 'Based on Fabrizio’s latest comments about Spain, it looks like Rashford’s ideal destination is Barcelona. I also think Arsenal/Chelsea will be interested in January, especially with Martinelli and Sterling’s poor season and Mudryk’s recent suspension from football. Where do you guys think he’ll go?', TRUE, '2024-12-18 12:00:00', NULL),
    (11, '🚨 - Rúben Amorim’s latest press conference had ZERO questions about the game against Spurs tomorrow. Instead, there were 11 questions about Marcus Rashford.', TRUE, '2024-12-19 00:00:00', NULL),
    (11, '🚨🗣️ - Rúben Amorim: "My goal is to set STANDARDS, see if the players are able to meet them, and then prepare for the matches. I am focused on that."', TRUE, '2024-12-19 00:05:00', NULL),
    (11, '“Really good. Trained really well. He seems a little bit upset with me and that is perfect. I was really, really happy because I would do the same and he is ready for this game.”', TRUE, '2024-12-19 00:10:00', NULL),
    (7, 'I don’t understand the concept of people turning to facebook for medical advice especially for their children wtf', TRUE, '2024-12-14 10:05:00', NULL),
    (2, 'would you guys still love taylor if she was a worm #taylorpede', TRUE, NOW(), 9), 
    (10, 'I drew Taylor to a T! :D #terrific', TRUE, NOW(), 9), 
    (10, 'Taylor Swift''s Wins Grammy!', TRUE, NOW(), 9);


INSERT INTO COMMENT (userID, message, createdDate, postID, parentCommentID) 
VALUES 
    (3, 'Your painting is amazing! What inspired you?', '2024-01-01 15:00:00', 3, NULL),
    (4, 'I need to try that recipe!', '2024-10-04 15:00:00', 4, NULL),
    (3, 'Can’t wait to see the finished product!', NOW(), 1, NULL),
    (2, 'I loved the view from the top of the mountain!', '2024-10-04 15:01:00', NULL, 2),
    (2, 'Absolutely stunning! I love your color choices!', '2024-01-01 15:05:00', 3, NULL),
    (5, 'That sounds delicious! What ingredients did you use?', NOW(), 4, NULL),
    (7, 'Sounds like a great hike! Where did you go?', NOW(), 1, NULL),
    (6, 'Can’t wait to hear your novel! What’s it about?', NOW(), 9, NULL),
    (8, 'What type of vegetables are you growing?', NOW(), 8, NULL),
    (3, 'I’d love to join you for a hike sometime!', NOW(), 1, NULL),
    (10, 'You should share more of your music! I’m sure it’s great!', NOW(), 10, NULL),
    (9, 'I’m working on a similar project! We should compare notes.', NOW(), 9, NULL),
    (10, 'Girl Math', NOW(), 6, NULL),
    (4, 'Your yoga practice is inspiring! What’s your favorite pose?', NOW(), 5, NULL),
    (5, 'Great to see you volunteering! Those animals need love!', NOW(), 14, NULL),
    (6, 'I’m also looking for solo travel tips! Let’s exchange ideas.', NOW(), 3, NULL),
    (8, 'Fantastic sunset! Nature’s beauty is unmatched.', NOW(), 17, NULL),
    (7, 'Good luck with your mewing tournament! Have fun!', NOW(), 18, NULL),
    (9, 'Food festivals are the best! What did you try?', NOW(), 19, NULL),
    (10, 'I love this idea! Can’t wait to see what you create.', NOW(), 16, NULL),
    (4, 'Excited for the holidays! Any plans yet?', NOW(), 15, NULL),
    (2, 'He doesn’t start ahead of Lamine or Raphina & Barca can’t afford him anyway.', '2024-12-18 15:00:00', 20, NULL),  
    (4, 'Rashford will cook like crazy at Barcelona lbr 😭', '2024-12-18 15:01:00', 20, NULL),  
    (5, 'Considering his wages only Chelsea, PSG and Saudi seems appropriate. Maybe Newcastle too', '2024-12-18 15:02:00', 20, NULL),  
    (6, 'Hola, soy Marcos Rashford', '2024-12-18 15:03:00', 20, NULL),  
    (7, 'We shouldn’t be selling him to premier league rivals who are challenging for the title.', '2024-12-18 15:04:00', 20, NULL),
    (6, 'Atletico maybe?', '2024-12-18 17:26:00', NULL, 22),
    (7, 'how sweet! 🥰🥰😍', '2024-12-18 17:26:00', 25, NULL); 

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
    (10, NOW(), NULL, 2),
    (2, NOW(), 20, NULL),   
    (3, NOW(), 20, NULL),  
    (4, NOW(), 20, NULL),  
    (5, NOW(), 20, NULL),  
    (6, NOW(), 20, NULL),   
    (7, NOW(), 20, NULL),   
    (8, NOW(), 20, NULL),  
    (9, NOW(), 20, NULL), 
    (10, NOW(), 20, NULL),
    (11, NOW(), 1, NULL),
    (11, NOW(), 3, NULL),
    (11, NOW(), 5, NULL),
    (11, NOW(), NULL, 3),
    (11, NOW(), NULL, 5),
    (11, NOW(), NULL, 9),
    (11, NOW(), NULL, 11);

INSERT INTO FOLLOW (followerID, followeeID, state, followDate) VALUES
    (7, 2, 'Accepted', '2024-12-18 10:00:00'),
    (2, 10, 'Pending', '2024-12-18 11:00:00'),
    (4, 5, 'Accepted', '2024-12-18 12:00:00'),
    (6, 7, 'Accepted', '2024-12-18 13:00:00'),
    (2, 11, 'Accepted', '2024-12-18 14:00:00'),
    (3, 11, 'Accepted', '2024-12-18 15:00:00'),
    (6, 11, 'Accepted', '2024-12-18 16:00:00');

INSERT INTO NOTIFICATION (receiverID, date, seen, followID, commentID, likeID) VALUES
    (2, '2024-12-18 10:05:00', FALSE, 1, NULL, NULL),  
    (10, '2024-12-18 11:05:00', FALSE, 2, NULL, NULL), 
    (5, '2024-12-18 12:05:00', FALSE, 3, NULL, NULL),  
    (7, '2024-12-18 13:05:00', FALSE, 4, NULL, NULL),  
    (11, '2024-12-18 14:05:00', FALSE, 5, NULL, NULL), 
    (11, '2024-12-18 15:05:00', FALSE, 6, NULL, NULL), 
    (11, '2024-12-18 16:05:00', FALSE, 7, NULL, NULL); 

INSERT INTO BLOCK (blockerID, blockedID) VALUES
    (4, 5); 

INSERT INTO USER_TOPICS (userID, topicID) 
VALUES 
    (2, 1), 
    (2, 4), 
    (3, 2),   
    (4, 1),   
    (4, 3), 
    (5, 4), 
    (6, 2),   
    (7, 3);   

INSERT INTO USER_REPORTS (userID, postID, commentID, reason) VALUES
    (2, NULL, 1, 'Spam'),                 
    (3, 3, NULL, 'Hate speech'),          
    (4, NULL, 3, 'Offensive language');   

INSERT INTO GROUP_INVITATION (groupID, userID, createdDate) VALUES
    (3, 2, '2024-10-03 14:00:00'), 
    (3, 3, '2024-12-03 16:00:00'),
    (8, 3, NOW());

INSERT INTO POST_TOPICS (postID, topicID) 
VALUES 
    (1, 4), 
    (2, 6), 
    (3, 5), 
    (4, 4), 
    (5, 6), 
    (6, 3), 
    (7, 8), 
    (8, 6), 
    (9, 9), 
    (10, 3),
    (11, 6),
    (12, 4),
    (13, 5),
    (14, 4),
    (15, 9),
    (16, 6),
    (17, 8),
    (18, 5),
    (19, 4),
    (20, 14),
    (20, 13),
    (20, 7);

INSERT INTO MEDIA (path, postID, commentID, userID) 
VALUES 
    ('images/kNmEQPAAOLWmeP2S8IgcpRmUpWzjKLqk4Sq71R0r.jpg', 20, NULL, NULL),
    ('images/8QYuyrGxDqEHQmrrOeosTuHhgz5wKpX23kXqP0ZY.jpg', NULL, 25, NULL),
    ('images/profile11.jpg', NULL, NULL, 11),
    ('images/banner11.jpg', NULL, NULL, 11),
    ('images/profile7.jpg', NULL, NULL, 7),
    ('images/W0Iq8td3d876eBLr9oYc5qlJy2SrJ6aJDPuqeWYN.jpg', 27, NULL, NULL),
    ('images/iuHdxsvZ3gG9ZVvK8JoRJfmk3HEXeLD4gBR6Ip5l.jpg', 25, NULL, NULL);


