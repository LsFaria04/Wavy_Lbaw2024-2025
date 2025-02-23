
INSERT INTO Users (userName, passwordHash, bio, email, state, visibilityPublic, isAdmin) 
VALUES 
    ('Admin', '$2y$10$ucilw0DqyGGYCybQLesgLOCCZLy07yOwMcdLMrU50yYPTTxLMg37C', 'Community leader passionate about making a difference.', 'administrator@example.com', 'active', FALSE, TRUE),
    ('BobJohnson', '$2y$10$ucilw0DqyGGYCybQLesgLOCCZLy07yOwMcdLMrU50yYPTTxLMg37C', 'Avid hiker and nature enthusiast, always seeking the next adventure.', 'bob.johnson@example.com', 'active', TRUE, FALSE),
    ('CharlieBrown', '$2y$10$ucilw0DqyGGYCybQLesgLOCCZLy07yOwMcdLMrU50yYPTTxLMg37C', 'Art lover who enjoys visiting galleries and creating new pieces.', 'charlie.brown@example.com', 'suspended', TRUE, FALSE),
    ('DanaWhite', '$2y$10$ucilw0DqyGGYCybQLesgLOCCZLy07yOwMcdLMrU50yYPTTxLMg37C', 'Foodie and chef, always experimenting with new recipes.', 'dana.white@example.com', 'active', TRUE, FALSE),
    ('EveBlack', '$2y$10$ucilw0DqyGGYCybQLesgLOCCZLy07yOwMcdLMrU50yYPTTxLMg37C', 'Traveler at heart, exploring cultures and cuisines around the world.', 'eve.black@example.com', 'active', TRUE, FALSE),
    ('FrankMoore', '$2y$10$ucilw0DqyGGYCybQLesgLOCCZLy07yOwMcdLMrU50yYPTTxLMg37C', 'Sports coach who enjoys mentoring young athletes and staying active.', 'frank.moore@example.com', 'active', TRUE, FALSE),
    ('GraceHall', '$2y$10$ucilw0DqyGGYCybQLesgLOCCZLy07yOwMcdLMrU50yYPTTxLMg37C', 'Yoga instructor and wellness advocate, passionate about mindfulness.', 'grace.hall@example.com', 'active', TRUE, FALSE),
    ('HenryKing', '$2y$10$ucilw0DqyGGYCybQLesgLOCCZLy07yOwMcdLMrU50yYPTTxLMg37C', 'Farmer with a love for growing fresh vegetables and herbs.', 'henry.king@example.com', 'suspended', TRUE, FALSE),
    ('IvyAdams', '$2y$10$ucilw0DqyGGYCybQLesgLOCCZLy07yOwMcdLMrU50yYPTTxLMg37C', 'Bookworm and aspiring novelist, always lost in a good story.', 'ivy.adams@example.com', 'active', TRUE, FALSE),
    ('JackLee', '$2y$10$ucilw0DqyGGYCybQLesgLOCCZLy07yOwMcdLMrU50yYPTTxLMg37C', 'Music enthusiast and amateur guitarist, loves performing at local cafes.', 'jack.lee@example.com', 'active', FALSE, FALSE),
    ('Frank', '$2y$10$ucilw0DqyGGYCybQLesgLOCCZLy07yOwMcdLMrU50yYPTTxLMg37C', 'MUFC Fan Account • NOT Affiliated with Rúben Amorim • DM for promos/ads etc', 'frank@example.com', 'active', TRUE, FALSE),
    ('JonnyBones', '$2y$10$ucilw0DqyGGYCybQLesgLOCCZLy07yOwMcdLMrU50yYPTTxLMg37C', 'John "Bones" Jones • UFC Heavyweight Champion • I love to duck • Dana’s Favorite Son', 'jonnybones@example.com', 'active', TRUE, FALSE);

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
    (10, 10),
    (10, 9),
    (10, 8),
    (10, 7),
    (10, 6),
    (10, 5),
    (10, 4),
    (10, 3),
    (10, 2),
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
    (2, 'would you guys still love taylor if she was a worm #taylorpede', TRUE, '2024-12-17 10:05:00', 9), 
    (10, 'I drew Taylor to a T! :D #terrific', TRUE, NOW(), 9), 
    (10, 'Taylor Swift''s Wins Grammy!', TRUE, NOW(), 9),
    (11, 'Welcome, we can freely discuss everything related to United here.', TRUE, '2024-12-20 09:00:00', 10),
    (11, 'What a performance by Rashford last night! Truly world-class.', TRUE, '2024-12-20 10:00:00', 10),
    (11, 'We really need reinforcements in defense this January. Thoughts?', TRUE, '2024-12-20 12:30:00', 10),
    (6, 'Ruben is changing the culture!!', TRUE, '2024-12-20 14:00:00', 10),
    (7, 'Looking forward to the next match! Hoping to see more from Garnacho.', TRUE, '2024-12-20 16:45:00', 10),
    (4, 'Ruben’s tactics are starting to really show their worth. What do you guys think?', TRUE, '2024-12-20 18:00:00', 10),
    (5, 'Does anyone else think that Bruno deserves more credit for his leadership?', TRUE, '2024-12-20 19:30:00', 10),
    (6, 'What’s up with City?', TRUE, '2024-12-20 21:00:00', 10),
    (7, 'How amazing was that Casemiro header? Pure class!', TRUE, '2024-12-21 09:00:00', 10),
    (8, 'Love this man!', TRUE, '2024-12-21 11:00:00', 10),
    (9, 'What’s everyone’s prediction for the game against Arsenal?', TRUE, '2024-12-21 13:00:00', 10),
    (10, 'Not sure if there are any sports cards collectors here. But I just picked this up. Couldn’t be happier. My current favorite player on our team ❤️ ', TRUE, '2024-12-21 15:00:00', 10),
    (3, 'Visited an amazing art gallery today! So inspired to create something new.', TRUE, '2024-12-22 18:41:54', NULL),
    (4, 'Just cooked up a new recipe: spicy truffle pasta! Who wants the recipe?', TRUE, '2024-12-22 18:46:54', NULL),
    (12, 'Training camp is going strong! Feeling ready for the next challenge. #UFC #Champion', TRUE, '2024-12-22 18:51:54', NULL);

INSERT INTO COMMENT (userID, message, createdDate, postID, parentCommentID) 
VALUES 
    (3, 'That looks absolutely delicious! Would you recommend it for a beginner cook? 🍳😊', '2024-01-01 15:00:00', 3, NULL),
    (4, 'I need to try that recipe!', '2024-10-04 15:00:00', 4, NULL),
    (3, 'Sounds incredible! Nothing beats the fresh mountain air and those breathtaking views. 🏔️🌲', NOW(), 1, NULL),
    (2, 'I loved the view from the top of the mountain!', '2024-10-04 15:01:00', NULL, 2),
    (2, 'That looks mouthwatering! 🍽️ Care to share the recipe? I’d love to try making it!', '2024-01-01 15:05:00', 3, NULL),
    (5, 'That sounds delicious! What ingredients did you use?', NOW(), 4, NULL),
    (7, 'Sounds like a great hike! Where did you go?', NOW(), 1, NULL),
    (6, 'Can’t wait to hear your novel! What’s it about?', NOW(), 9, NULL),
    (8, 'That’s amazing! Writing a novel is no small feat—keep the creativity flowing! 📚✨', NOW(), 8, NULL),
    (3, 'I’d love to join you for a hike sometime!', NOW(), 1, NULL),
    (10, 'Great job pushing through the challenge! Keep up the awesome work—you’re inspiring! 💪🔥', NOW(), 10, NULL),
    (9, 'I’m working on a similar project! We should compare notes.', NOW(), 9, NULL),
    (10, 'Girl Math', NOW(), 6, NULL),
    (4, 'That’s awesome! Coaching is such a rewarding experience—your team is lucky to have you! 💪🏆', NOW(), 5, NULL),
    (5, 'Great to see you volunteering! Those animals need love!', NOW(), 14, NULL),
    (6, 'I’m also looking for solo travel tips! Let’s exchange ideas.', NOW(), 19, NULL),
    (8, 'That’s awesome! A sunset painting must look stunning. Can’t wait to see it!', NOW(), 16, NULL),
    (7, 'Good luck with your mewing tournament! Have fun!', NOW(), 17, NULL),
    (9, 'Food festivals are the best! Have you tried them?', NOW(), 19, NULL),
    (10, 'That’s amazing! I’d love to see it. There’s something so calming about sunset colors. What medium did you use for the painting?', NOW(), 16, NULL),
    (4, 'That’s awesome! I’d recommend checking out sites like FreeCodeCamp for structured courses and projects, or LeetCode for practicing algorithms. Also, don’t forget about Stack Overflow for any coding questions you might have. Keep it up!', NOW(), 15, NULL),
    (2, 'He doesn’t start ahead of Lamine or Raphina & Barca can’t afford him anyway.', '2024-12-18 15:00:00', 20, NULL),  
    (4, 'Rashford will cook like crazy at Barcelona lbr 😭', '2024-12-18 15:01:00', 20, NULL),  
    (5, 'Considering his wages only Chelsea, PSG and Saudi seems appropriate. Maybe Newcastle too', '2024-12-18 15:02:00', 20, NULL),  
    (6, 'Hola, soy Marcos Rashford', '2024-12-18 15:03:00', 20, NULL),  
    (7, 'We shouldn’t be selling him to premier league rivals who are challenging for the title.', '2024-12-18 15:04:00', 20, NULL),
    (6, 'Atletico maybe?', '2024-12-18 17:26:00', NULL, 22),
    (7, 'how sweet! 🥰🥰😍', '2024-12-19 8:26:00', 25, NULL),
    
    /* comment id 29*/(2, 'Rashford was incredible! That goal will be remembered for ages.', '2024-12-20 10:30:00', 29, NULL),
    (3, 'Absolutely agree! He’s been our standout player this season.', '2024-12-20 10:45:00', 29, NULL),
    (4, 'The chemistry between Rashford and Bruno is unreal!', '2024-12-20 11:00:00', 29, NULL),
    (5, 'Let’s hope he keeps this form up!', '2024-12-20 11:15:00', 29, NULL),
    (6, 'We desperately need a center-back. Who should we go for?', '2024-12-20 12:45:00', 30, NULL),
    (7, 'I think Timber would be a great addition.', '2024-12-20 13:00:00', 30, NULL),
    (8, 'Maybe we should try for Pau Torres again?', '2024-12-20 13:15:00', 30, NULL),
    (9, 'We also need a solid backup for Dalot.', '2024-12-20 13:30:00', 30, NULL),
    (10, 'Last weekend’s win was a testament to Ruben’s brilliance!', '2024-12-20 14:15:00', 31, NULL),
    (11, 'Agreed! The team really showed character.', '2024-12-20 14:30:00', 31, NULL),
    (2, 'Still can’t believe that comeback!', '2024-12-20 14:45:00', 31, NULL),
    (3, 'I’m so proud of this team!', '2024-12-20 15:00:00', 31, NULL),
    (4, 'Garnacho has so much potential! Hope he gets more minutes.', '2024-12-20 17:00:00', 32, NULL),
    (5, 'He’s a future star for sure.', '2024-12-20 17:15:00', 32, NULL),
    (6, 'Can’t wait to see him play in the next match!', '2024-12-20 17:30:00', 32, NULL),
    (7, 'His dribbling is insane for his age.', '2024-12-20 17:45:00', 32, NULL),
    (8, 'Ruben has transformed this team. Tactics on point!', '2024-12-20 18:15:00', 33, NULL),
    (9, 'His substitutions have been brilliant too.', '2024-12-20 18:30:00', 33, NULL),
    (10, 'Finally, we have a manager who understands the club.', '2024-12-20 18:45:00', 33, NULL),
    (11, 'Let’s hope he brings us some trophies soon!', '2024-12-20 19:00:00', 33, NULL),
    (2, 'Old Trafford is a fortress!', '2024-12-21 12:00:00', 34, NULL),
    (3, 'Don’t ask questions. Just drink the tears of all 5 of their fans LMAO', '2024-12-21 12:15:00', 35, NULL),
    (4, 'We need to strengthen our midfield depth.', '2024-12-21 13:30:00', 36, NULL),
    (2, 'That header was pure class! Casemiro is a beast in the air.', '2024-12-21 09:15:00', 36, NULL),
    (3, 'His experience really shines in moments like that.', '2024-12-21 09:30:00', 36, NULL),
    (4, 'We needed that goal so badly. What a player!', '2024-12-21 09:45:00', 36, NULL),
    (5, 'Casemiro’s leadership is unmatched. He’s so reliable.', '2024-12-21 10:00:00', 36, NULL),
    (6, 'Ruben is the best thing that happened to this team during these terrible years without Fergie ♥️♥️♥️', '2024-12-21 11:15:00', 37, NULL),
    (7, 'The genuine excitement of winning a derby in this fashion is probably one of the greatest feelings from either side in any derby.', '2024-12-21 11:30:00', 37, NULL),
    (8, 'Casemiro lol', NOW(), 37, NULL),
    (9, 'I think we’ll win 2-1. Rashford and Bruno to score.', '2024-12-21 13:15:00', 38, NULL),
    (10, 'This will be a tough game, but I believe in our squad.', '2024-12-21 13:30:00', 38, NULL),
    (2, 'Arsenal is strong, but we can counter them effectively.', '2024-12-21 13:45:00', 38, NULL),
    (3, 'I’m predicting a clean sheet for Onana. 1-0 United!', '2024-12-21 14:00:00', 38, NULL),
    (4, 'As long as we stay compact defensively, we’ll win.', '2024-12-21 14:15:00', 38, NULL),
    (5, 'Looking forward to Bruno bossing the midfield.', '2024-12-21 14:30:00', 38, NULL),
    (6, 'This squad has so much potential. We’re just getting started!', '2024-12-21 15:15:00', 39, NULL),
    (7, 'ETH’s biggest trick was to make us believe we were never even in the UCL last season.', '2024-12-21 15:30:00', 39, NULL),
    (8, 'Get it PSA graded if you’re in the US!', '2024-12-21 15:45:00', 39, NULL),
    (9, 'i used to be a card collector and my best purchase was a signed ogs card that is still at the front of my folder today ', '2024-12-21 16:00:00', 39, NULL),
    (10, 'Im in America and baseball cards are big business. I didn’t know Topps finest made soccer cards. That’s a beauty ', '2024-12-21 16:15:00', 39, NULL),
    (2, 'I’ve been saying for a while the Hojlund will thrive under Amorim - could be worth a pretty penny in time to come.', '2024-12-21 16:30:00', 39, NULL),
    (3, 'I love Rasmus as well, no need for a new striker, invest the money in lcb/lwb/lcam, our left side is lacking compared to the right', NOW(), 39, NULL),
    (4, 'We were in the champions league last season? How much did I drink to forget that? ', NOW(), 39, NULL),
    (2, 'I agree, Barcelona seems like a great fit for Rashford. They need a player like him.', '2024-12-22 17:20:00', 20, NULL),
    (3, 'Arsenal or Chelsea could really use him, but their poor seasons might hurt their chances.', '2024-12-22 17:25:00', 20, NULL),
    (4, 'I think Barcelona will make a strong push, especially with their financial troubles. They’ll need a player like Rashford.', '2024-12-22 17:30:00', 20, NULL),
    (5, 'Seems like Rashford is stealing the spotlight! That''s a lot of focus for one player before a big game.', '2024-12-22 18:00:00', 21, NULL),
    (6, 'Rashford''s future is certainly the talk of the town. Wonder if it''ll distract from the Spurs game?', '2024-12-22 17:21:00', 21, NULL),
    (7, 'Amorim must''ve been frustrated with all those questions! The game against Spurs should be the focus right now.', '2024-12-22 18:00:00', 21, NULL),
    (5, 'That dish looks amazing! What ingredients did you use?', NOW(), 40, NULL),
    (6, 'I need to try this recipe! Can you share it?', NOW(), 40, NULL),
    (7, 'Amazing fight, champ! You’re an inspiration.', NOW(), 41, NULL),
    (8, 'When’s the next title defense? Can’t wait to watch!', NOW(), 41, NULL),
    (9, 'What’s your favorite post-fight meal, Jonny?', NOW(), 41, NULL),
    (10, 'Your art is so inspiring, Charlie! What medium did you use?', NOW(), 42, NULL),
    (11, 'I’d love to see more of your pieces. Are they displayed somewhere?', NOW(), 42, NULL),
    (2, 'This is so creative! Can you share the inspiration behind it?', NOW(), 42, NULL),
    (3, 'Your gallery visits always seem exciting! Any recommendations?', NOW(), 42, NULL);

INSERT INTO LIKES (userID, createdDate, postID, commentID) 
VALUES 
    (2, NOW(), 1, NULL),   
    (3, NOW(), 1, NULL),   
    (4, NOW(), 1, NULL),   
    (5, NOW(), 1, NULL),   
    (6, NOW(), 1, NULL),   
    (7, NOW(), 1, NULL),   
    
    (2, NOW(), 2, NULL),  
    (3, NOW(), 2, NULL),  
    (4, NOW(), 2, NULL),  
    (5, NOW(), 2, NULL),  
    (6, NOW(), 2, NULL),  
    
    (7, NOW(), 3, NULL),   
    (8, NOW(), 3, NULL),   
    (9, NOW(), 3, NULL),   
    (10, NOW(), 3, NULL),   
    (11, NOW(), 3, NULL),   
    
    (2, NOW(), 4, NULL),   
    (3, NOW(), 4, NULL),   
    (4, NOW(), 4, NULL),   
    (5, NOW(), 4, NULL),   
    (6, NOW(), 4, NULL),   
    
    (7, NOW(), 5, NULL),   
    (8, NOW(), 5, NULL),   
    (9, NOW(), 5, NULL),   
    (10, NOW(), 5, NULL),   
    
    (2, NOW(), 6, NULL),   
    (3, NOW(), 6, NULL),   
    (4, NOW(), 6, NULL),   
    (5, NOW(), 6, NULL),   
    
    (10, NOW(), 8, NULL),   
    (11, NOW(), 8, NULL),   
    (2, NOW(), 8, NULL),   
    (3, NOW(), 8, NULL),   
    
    (4, NOW(), 9, NULL),   
    (5, NOW(), 9, NULL),   
    (6, NOW(), 9, NULL),   
    (7, NOW(), 9, NULL),   
    
    (8, NOW(), 10, NULL),   
    (9, NOW(), 10, NULL),   
    (10, NOW(), 10, NULL),   
    (11, NOW(), 10, NULL),   
    
    (2, NOW(), 11, NULL),   
    (3, NOW(), 11, NULL),   
    (4, NOW(), 11, NULL),   
    (5, NOW(), 11, NULL),   
    
    (6, NOW(), 12, NULL),   
    (7, NOW(), 12, NULL),   
    (8, NOW(), 12, NULL),   
    (9, NOW(), 12, NULL),   
    
    (10, NOW(), 13, NULL),   
    (11, NOW(), 13, NULL),   
    (2, NOW(), 13, NULL), 
    
    (4, NOW(), 14, NULL),   
    (5, NOW(), 14, NULL),   
    (6, NOW(), 14, NULL),   
    (7, NOW(), 14, NULL),   
    
    (8, NOW(), 14, NULL),   
    (9, NOW(), 14, NULL),   
    (10, NOW(), 14, NULL),   
    (11, NOW(), 14, NULL),   
    
    (2, NOW(), 15, NULL),   
    (3, NOW(), 15, NULL),   
    (4, NOW(), 15, NULL),   
    (5, NOW(), 15, NULL),   
    
    (6, NOW(), 16, NULL),   
    (7, NOW(), 16, NULL),   
    (8, NOW(), 16, NULL),   
    (9, NOW(), 16, NULL),   
    
    (10, NOW(), 17, NULL),   
    (11, NOW(), 17, NULL),   
    (2, NOW(), 17, NULL),   

    (4, NOW(), 18, NULL),   
    (5, NOW(), 18, NULL),   
    (6, NOW(), 18, NULL),   
    (7, NOW(), 18, NULL),   
    
    (8, NOW(), 19, NULL),   
    (9, NOW(), 19, NULL),   
    (10, NOW(), 19, NULL),   
    (11, NOW(), 19, NULL),   
    
    (10, NOW(), 22, NULL),   
    (11, NOW(), 22, NULL),   
    (2, NOW(), 22, NULL),   

    (6, NOW(), 24, NULL),   
    (7, NOW(), 24, NULL),   
    (8, NOW(), 24, NULL),   
    (9, NOW(), 24, NULL),   

    (3, NOW(), NULL, 1),  
    (5, NOW(), NULL, 2),  
    (7, NOW(), NULL, 3),   
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
    (11, NOW(), NULL, 3),
    (11, NOW(), NULL, 5),
    (11, NOW(), NULL, 9),
    (11, NOW(), NULL, 11),

    (7, NOW(), 28, NULL),
    (8, NOW(), 28, NULL), 
    (4, NOW(), 28, NULL), 

    (7, NOW(), 29, NULL),
    (8, NOW(), 29, NULL), 
    (4, NOW(), 29, NULL), 
    (5, NOW(), 29, NULL), 
    (6, NOW(), 29, NULL), 

    (7, NOW(), 30, NULL), 
    (5, NOW(), 30, NULL), 
    (6, NOW(), 30, NULL),

    (7, NOW(), 31, NULL), 
    (8, NOW(), 31, NULL), 
    (4, NOW(), 31, NULL), 
    (5, NOW(), 31, NULL), 
    (6, NOW(), 31, NULL), 

    (7, NOW(), 32, NULL), 
    (8, NOW(), 32, NULL), 
    (4, NOW(), 32, NULL), 
    (6, NOW(), 32, NULL), 

    (7, NOW(), 33, NULL), 
    (8, NOW(), 33, NULL), 
    (4, NOW(), 33, NULL), 
    (5, NOW(), 33, NULL), 
    (6, NOW(), 33, NULL),
    (2, NOW(), 33, NULL),

    (5, NOW(), 34, NULL),
    (6, NOW(), 34, NULL),
    (7, NOW(), 34, NULL),

    (7, NOW(), 35, NULL),
    (8, NOW(), 35, NULL),
    (4, NOW(), 35, NULL),
    (3, NOW(), 35, NULL),

    (7, NOW(), 36, NULL),
    (8, NOW(), 36, NULL),
    (4, NOW(), 36, NULL),
    (5, NOW(), 36, NULL),

    (6, NOW(), 37, NULL),
    (8, NOW(), 37, NULL),
    (10, NOW(), 37, NULL),
    (7, NOW(), 37, NULL),
    (5, NOW(), 37, NULL),
    (4, NOW(), 37, NULL),

    (7, NOW(), 38, NULL),
    (8, NOW(), 38, NULL),
    (4, NOW(), 38, NULL),
    (5, NOW(), 38, NULL),
    (10, NOW(), 38, NULL),
    (11, NOW(), 38, NULL),
    (2, NOW(), 38, NULL),

    (7, NOW(), 39, NULL),
    (9, NOW(), 39, NULL),
    (5, NOW(), 39, NULL),
    (10, NOW(), 39, NULL),
    (11, NOW(), 39, NULL),
    (2, NOW(), 39, NULL),
    (8, NOW(), 39, NULL),
    (4, NOW(), 39, NULL),

    (7, NOW(), NULL, 29),
    (8, NOW(), NULL, 29),
    (4, NOW(), NULL, 29),
    
    (5, NOW(), NULL, 30),
    (6, NOW(), NULL, 30),
    (3, NOW(), NULL, 30),

    (7, NOW(), NULL, 31),
    (8, NOW(), NULL, 31),

    (4, NOW(), NULL, 32),
    (6, NOW(), NULL, 32),
    (7, NOW(), NULL, 32),

    (5, NOW(), NULL, 33),
    (8, NOW(), NULL, 33),
    (10, NOW(), NULL, 33),

    (3, NOW(), NULL, 34),
    (5, NOW(), NULL, 34),
    (6, NOW(), NULL, 34),

    (8, NOW(), NULL, 35),
    (7, NOW(), NULL, 35),
    (4, NOW(), NULL, 35),
    (10, NOW(), NULL, 35),

    (9, NOW(), NULL, 36),
    (2, NOW(), NULL, 36),

    (5, NOW(), NULL, 37),
    (6, NOW(), NULL, 37),
    (10, NOW(), NULL, 37),

    (3, NOW(), NULL, 38),
    (4, NOW(), NULL, 38),
    (6, NOW(), NULL, 38),
    (10, NOW(), NULL, 38),
    (9, NOW(), NULL, 38),

    (7, NOW(), NULL, 39),
    (8, NOW(), NULL, 39),
    (4, NOW(), NULL, 39),

    (5, NOW(), NULL, 40),
    (3, NOW(), NULL, 40),
    (6, NOW(), NULL, 40),
    (10, NOW(), NULL, 40),

    (7, NOW(), NULL, 41),
    (8, NOW(), NULL, 41),
    (5, NOW(), NULL, 41),

    (4, NOW(), NULL, 42),
    (6, NOW(), NULL, 42),
    (3, NOW(), NULL, 42),
    (10, NOW(), NULL, 42),

    (9, NOW(), NULL, 43),
    (10, NOW(), NULL, 43),
    (8, NOW(), NULL, 43),

    (5, NOW(), NULL, 44),
    (7, NOW(), NULL, 44),

    (8, NOW(), NULL, 45),
    (6, NOW(), NULL, 45),
    (10, NOW(), NULL, 45),

    (3, NOW(), NULL, 46),
    (5, NOW(), NULL, 46),
    (7, NOW(), NULL, 46),

    (3, NOW(), NULL, 47),
    (5, NOW(), NULL, 47),
    (7, NOW(), NULL, 47),
    (4, NOW(), NULL, 47),
    (6, NOW(), NULL, 47),
    (9, NOW(), NULL, 47),

    (3, NOW(), NULL, 48),
    (5, NOW(), NULL, 48),
    (7, NOW(), NULL, 48),
    (4, NOW(), NULL, 48),

    (3, NOW(), NULL, 49),

    (3, NOW(), NULL, 50),
    (5, NOW(), NULL, 50),
    (7, NOW(), NULL, 50),

    (3, NOW(), NULL, 51),
    (5, NOW(), NULL, 51),
    (7, NOW(), NULL, 51),
    (4, NOW(), NULL, 51),
    (6, NOW(), NULL, 51),
    (9, NOW(), NULL, 51),

    (7, NOW(), NULL, 52),
    (8, NOW(), NULL, 52),
    (9, NOW(), NULL, 54),
    (4, NOW(), NULL, 54),
    (5, NOW(), NULL, 54),
    (6, NOW(), NULL, 54),
    (7, NOW(), NULL, 55),
    (8, NOW(), NULL, 55),

    (3, NOW(), NULL, 56),
    (5, NOW(), NULL, 56),
    (6, NOW(), NULL, 57),
    (8, NOW(), NULL, 58),
    (9, NOW(), NULL, 58),
    (10, NOW(), NULL, 58),

    (6, NOW(), NULL, 59),
    (8, NOW(), NULL, 60),
    (9, NOW(), NULL, 60),
    (3, NOW(), NULL, 60),
    (7, NOW(), NULL, 61),
    (10, NOW(), NULL, 62),
    (2, NOW(), NULL, 62),
    (4, NOW(), NULL, 62),
    (5, NOW(), NULL, 63),
    (6, NOW(), NULL, 63),

    (5, NOW(), NULL, 64),
    (6, NOW(), NULL, 64),
    (7, NOW(), NULL, 65),
    (8, NOW(), NULL, 65),
    (9, NOW(), NULL, 65),
    (3, NOW(), NULL, 66),
    (10, NOW(), NULL, 67),
    (2, NOW(), NULL, 68),
    (4, NOW(), NULL, 69),
    (5, NOW(), NULL, 69),
    (6, NOW(), NULL, 70),
    (7, NOW(), NULL, 70),
    (8, NOW(), NULL, 70),
    (9, NOW(), NULL, 70),
    (10, NOW(), NULL, 71),
    (3, NOW(), NULL, 72),
    (2, NOW(), NULL, 72),
    (4, NOW(), NULL, 72),

    (2, NOW(), 40, NULL),
    (3, NOW(), 40, NULL),
    (4, NOW(), 40, NULL),
    (5, NOW(), 40, NULL),
    (6, NOW(), 40, NULL),
    (7, NOW(), 40, NULL),
    (8, NOW(), 40, NULL),
    (9, NOW(), 40, NULL),
    (10, NOW(), 40, NULL),
    (11, NOW(), 40, NULL),

    (2, NOW(), 41, NULL),
    (3, NOW(), 41, NULL),
    (4, NOW(), 41, NULL),
    (5, NOW(), 41, NULL),
    (6, NOW(), 41, NULL),
    (7, NOW(), 41, NULL),
    (8, NOW(), 41, NULL),
    (9, NOW(), 41, NULL),
    (10, NOW(), 41, NULL),

    (2, NOW(), 42, NULL),
    (3, NOW(), 42, NULL),
    (4, NOW(), 42, NULL),
    (5, NOW(), 42, NULL),
    (6, NOW(), 42, NULL),
    (7, NOW(), 42, NULL),
    (8, NOW(), 42, NULL),

    (3, '2024-12-22 18:25:00', 21, NULL),
    (4, '2024-12-22 18:30:00', 22, NULL),
    (5, '2024-12-22 18:35:00', 23, NULL),
    (7, '2024-12-22 18:45:00', 21, NULL),
    (8, '2024-12-22 18:50:00', 22, NULL),
    (9, '2024-12-22 18:55:00', 23, NULL),
    (11, '2024-12-22 19:05:00', 21, NULL);


        

INSERT INTO FOLLOW (followerID, followeeID, state, followDate) VALUES
    (7, 2, 'Accepted', '2024-12-18 10:00:00'),
    (2, 10, 'Pending', '2024-12-18 11:00:00'),
    (4, 5, 'Accepted', '2024-12-18 12:00:00'),
    (6, 7, 'Accepted', '2024-12-18 13:00:00'),
    (2, 11, 'Accepted', '2024-12-18 14:00:00'),
    (3, 11, 'Accepted', '2024-12-18 15:00:00'),
    (4, 11, 'Accepted', '2024-12-18 16:00:00'),
    (5, 11, 'Accepted', '2024-12-18 14:00:00'),
    (6, 11, 'Accepted', '2024-12-18 15:00:00'),
    (7, 11, 'Accepted', '2024-12-18 14:00:00'),
    (8, 11, 'Accepted', '2024-12-18 15:00:00'),
    (9, 11, 'Accepted', '2024-12-18 16:00:00'),
    (10, 11, 'Accepted', '2024-12-18 14:00:00'),
    (12, 11, 'Accepted', '2024-12-18 15:00:00'),
    (2, 3, 'Accepted', '2024-12-19 10:30:00'),
    (7, 6, 'Accepted', '2024-12-19 12:00:00'),
    (8, 7, 'Accepted', '2024-12-19 13:00:00'),
    (5, 9, 'Accepted', '2024-12-19 14:00:00'),
    (9, 10, 'Pending', '2024-12-19 15:00:00'),
    (2, 7, 'Accepted', '2024-12-19 16:00:00'),
    (4, 8, 'Accepted', '2024-12-19 17:00:00'),
    (10, 2, 'Pending', '2024-12-19 19:00:00'),
    (8, 3, 'Accepted', '2024-12-19 20:00:00'),
    (5, 2, 'Accepted', '2024-12-19 21:00:00'),
    (7, 9, 'Accepted', '2024-12-19 22:00:00'),
    (2, 6, 'Accepted', '2024-12-19 23:00:00'),
    (4, 10, 'Pending', '2024-12-20 00:30:00'),
    (9, 6, 'Accepted', '2024-12-20 01:00:00'),
    (2, 8, 'Accepted', '2024-12-20 02:00:00'),
    (3, 6, 'Accepted', '2024-12-20 03:00:00'),
    (10, 3, 'Pending', '2024-12-20 04:00:00'),
    (11, 4, 'Accepted', '2024-12-22 10:00:00'),
    (11, 7, 'Accepted', '2024-12-22 10:00:00'),
    (11, 3, 'Accepted', '2024-12-22 10:00:00'),
    (11, 9, 'Accepted', '2024-12-22 10:00:00'),
    (11, 12, 'Accepted', '2024-12-22 10:00:00');


INSERT INTO NOTIFICATION (receiverID, date, seen, followID, commentID, likeID) VALUES
    (2, '2024-12-18 10:05:00', FALSE, 1, NULL, NULL),  
    (10, '2024-12-18 11:05:00', FALSE, 2, NULL, NULL), 
    (5, '2024-12-18 12:05:00', FALSE, 3, NULL, NULL),  
    (7, '2024-12-18 13:05:00', FALSE, 4, NULL, NULL),  
    (11, '2024-12-18 14:05:00', FALSE, 5, NULL, NULL), 
    (11, '2024-12-18 15:05:00', FALSE, 6, NULL, NULL), 
    (11, '2024-12-18 16:05:00', FALSE, 7, NULL, NULL); 

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
    (2, 4, NOW()),
    (4, 4, NOW()),
    (5, 4, NOW()),
    (6, 4, NOW()),
    (7, 4, NOW()),
    (8, 4, NOW()),
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
    (20, 7),
    (28, 14),
    (28, 13),
    (29, 14),
    (29, 13),
    (30, 14),
    (30, 13),
    (31, 14),
    (31, 13),
    (32, 14),
    (32, 13),
    (33, 14),
    (33, 13),
    (34, 14),
    (34, 13),
    (35, 14),
    (35, 13),
    (36, 14),
    (36, 13),
    (37, 14),
    (37, 13),
    (38, 14),
    (38, 13),
    (39, 14),
    (39, 13);

INSERT INTO MEDIA (path, postID, commentID, userID) 
VALUES 
    ('images/kNmEQPAAOLWmeP2S8IgcpRmUpWzjKLqk4Sq71R0r.jpg', 20, NULL, NULL),
    ('images/8QYuyrGxDqEHQmrrOeosTuHhgz5wKpX23kXqP0ZY.jpg', NULL, 25, NULL),
    ('images/W0Iq8td3d876eBLr9oYc5qlJy2SrJ6aJDPuqeWYN.jpg', 27, NULL, NULL),
    ('images/hnVykJfTYUnAGRkXlMo7JHK4tZtoGe9JP6DUDDrj.png', 39, NULL, NULL),
    ('images/G53aG15AolcXQiHurfmfgSrr3hDrpszgXqH5RCEJ.png', 39, NULL, NULL),
    ('images/smP9x1Gqbq0qdzFiBL0PvobajXsb1IeNIsPqvLss.png', 37, NULL, NULL),
    ('images/8K6euTnk7t1hPVvJyLWUegWynjmufWuLhif3f0gn.png', 35, NULL, NULL),
    ('images/iuHdxsvZ3gG9ZVvK8JoRJfmk3HEXeLD4gBR6Ip5l.jpg', 25, NULL, NULL),
    ('images/profile3.jpg', NULL, NULL, 3),
    ('images/banner3.jpg', NULL, NULL, 3),
    ('images/profile4.jpg', NULL, NULL, 4),
    ('images/banner4.jpg', NULL, NULL, 4),
    ('images/profile5.jpg', NULL, NULL, 5),
    ('images/banner5.jpg', NULL, NULL, 5),
    ('images/profile6.jpg', NULL, NULL, 6),
    ('images/banner6.jpg', NULL, NULL, 6),
    ('images/profile7.jpg', NULL, NULL, 7),
    ('images/banner7.jpg', NULL, NULL, 7),
    ('images/profile8.jpg', NULL, NULL, 8),
    ('images/banner8.jpg', NULL, NULL, 8),
    ('images/profile9.jpg', NULL, NULL, 9),
    ('images/banner9.jpg', NULL, NULL, 9),
    ('images/profile10.jpg', NULL, NULL, 10),
    ('images/banner10.jpg', NULL, NULL, 10),
    ('images/profile11.jpg', NULL, NULL, 11),
    ('images/banner11.jpg', NULL, NULL, 11),
    ('images/profile12.jpg', NULL, NULL, 12),
    ('images/banner12.jpg', NULL, NULL, 12),
    ('images/banner2.jpg', NULL, NULL, 2),
    ('images/profile2.jpg', NULL, NULL, 2),
    ('images/Ru2szQcjYuVqLNjBFsxPEhd6q0Hrj8h7Mks4Jnsq.jpg', 2, NULL, NULL),
    ('images/SKdRppttrLRBKnA89CQtWf3iuyb1JpSSWLmZ3Mlx.jpg', 3, NULL, NULL);
