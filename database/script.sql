CREATE SCHEMA IF NOT EXISTS lbaw2443;
SET search_path TO lbaw2443;


---------------------------- DROP FOR OLD DATABASE ---------------------------------------------------

DROP TABLE IF EXISTS USERS CASCADE;
DROP TABLE IF EXISTS MESSAGE CASCADE;
DROP TABLE IF EXISTS GROUPS CASCADE;  
DROP TABLE IF EXISTS GROUP_MEMBERSHIP CASCADE;
DROP TABLE IF EXISTS JOIN_GROUP_REQUEST CASCADE;
DROP TABLE IF EXISTS POST CASCADE;
DROP TABLE IF EXISTS TOPIC CASCADE;
DROP TABLE IF EXISTS COMMENT CASCADE;
DROP TABLE IF EXISTS LIKES CASCADE;  
DROP TABLE IF EXISTS MEDIA CASCADE;
DROP TABLE IF EXISTS NOTIFICATION CASCADE;
DROP TABLE IF EXISTS FOLLOW CASCADE;
DROP TABLE IF EXISTS BLOCK CASCADE;
DROP TABLE IF EXISTS USER_TOPICS CASCADE;
DROP TABLE IF EXISTS USER_REPORTS CASCADE;
DROP TABLE IF EXISTS GROUP_INVITATION CASCADE;
DROP TABLE IF EXISTS POST_TOPICS CASCADE;


------------------------------ TABLES -----------------------------------------------------------------

CREATE TABLE USERS (
    userID SERIAL PRIMARY KEY,
    userName VARCHAR(30) UNIQUE,
    passwordHash TEXT NOT NULL,
    bio TEXT DEFAULT '',
    email TEXT UNIQUE,
    state TEXT NOT NULL CHECK (state IN ('active', 'suspended', 'deleted')) DEFAULT 'active',
    visibilityPublic BOOLEAN NOT NULL DEFAULT TRUE,
    isAdmin BOOLEAN NOT NULL DEFAULT FALSE,
    remember_token VARCHAR(100) DEFAULT NULL,
    search TSVECTOR
);

CREATE TABLE MESSAGE (
    messageID SERIAL PRIMARY KEY,
    receiverID INTEGER NOT NULL,
    senderID INTEGER NOT NULL,
    message TEXT NOT NULL,
    date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (receiverID) REFERENCES USERS(userID) ON DELETE CASCADE,
    FOREIGN KEY (senderID) REFERENCES USERS(userID) ON DELETE CASCADE,
    CHECK (receiverID <> senderID)
);

CREATE TABLE GROUPS (
    groupID SERIAL PRIMARY KEY,
    groupName VARCHAR(30) NOT NULL,
    description TEXT DEFAULT '',
    visibilityPublic BOOLEAN NOT NULL DEFAULT TRUE,
    ownerID INTEGER NOT NULL,
    FOREIGN KEY (ownerID) REFERENCES USERS(userID) ON DELETE CASCADE,
    search TSVECTOR
);

CREATE TABLE GROUP_MEMBERSHIP (
    memberID SERIAL PRIMARY KEY,
    groupID INTEGER NOT NULL,
    userID INTEGER NOT NULL,
    FOREIGN KEY (groupID) REFERENCES GROUPS(groupID) ON DELETE CASCADE,
    FOREIGN KEY (userID) REFERENCES USERS(userID) ON DELETE CASCADE,
    UNIQUE (groupID, userID)
);

CREATE TABLE JOIN_GROUP_REQUEST (
    requestID SERIAL PRIMARY KEY,
    groupID INTEGER NOT NULL,
    userID INTEGER NOT NULL,
    createdDate TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (groupID) REFERENCES GROUPS(groupID) ON DELETE CASCADE,
    FOREIGN KEY (userID) REFERENCES USERS(userID) ON DELETE CASCADE,
    UNIQUE (groupID, userID)
);

CREATE TABLE POST (
    postID SERIAL PRIMARY KEY,
    userID INTEGER NOT NULL,
    message TEXT NOT NULL,
    visibilityPublic BOOLEAN NOT NULL DEFAULT TRUE,
    createdDate TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    groupID INTEGER DEFAULT NULL,
    FOREIGN KEY (userID) REFERENCES USERS(userID) ON DELETE CASCADE,
    FOREIGN KEY (groupID) REFERENCES GROUPS(groupID) ON DELETE SET NULL,
    search TSVECTOR
);

CREATE TABLE TOPIC (
    topicID SERIAL PRIMARY KEY,
    topicName VARCHAR(30) NOT NULL,
    search TSVECTOR
);

CREATE TABLE COMMENT (
    commentID SERIAL PRIMARY KEY,
    userID INTEGER NOT NULL,
    message TEXT NOT NULL,
    createdDate TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    postID INTEGER,
    parentCommentID INTEGER,  -- Parent comment if exists
    CHECK ((postID IS NOT NULL AND parentCommentID IS NULL) OR (postID IS NULL AND parentCommentID IS NOT NULL)),
    FOREIGN KEY (userID) REFERENCES USERS(userID) ON DELETE CASCADE,
    FOREIGN KEY (postID) REFERENCES POST(postID) ON DELETE CASCADE,
    FOREIGN KEY (parentCommentID) REFERENCES COMMENT(commentID) ON DELETE CASCADE,
    search TSVECTOR
);

CREATE TABLE LIKES (
    likeID SERIAL PRIMARY KEY,
    userID INTEGER NOT NULL,
    createdDate TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    postID INTEGER,
    commentID INTEGER,
    CHECK ((postID IS NOT NULL AND commentID IS NULL) OR (postID IS NULL AND commentID IS NOT NULL)),
    FOREIGN KEY (userID) REFERENCES USERS(userID) ON DELETE CASCADE,
    FOREIGN KEY (postID) REFERENCES POST(postID) ON DELETE CASCADE,
    FOREIGN KEY (commentID) REFERENCES COMMENT(commentID) ON DELETE CASCADE
);

CREATE TABLE MEDIA (
    mediaID SERIAL PRIMARY KEY,
    path TEXT UNIQUE NOT NULL,
    postID INTEGER,
    commentID INTEGER,
    userID INTEGER,
    CHECK (
        (postID IS NOT NULL AND commentID IS NULL and userID IS NULL) OR
        (postID IS NULL AND commentID IS NOT NULL and userID IS NULL) OR 
        (postID IS NULL AND commentID IS NULL and userID IS NOT NULL)
    ),
    FOREIGN KEY (postID) REFERENCES POST(postID) ON DELETE CASCADE,
    FOREIGN KEY (commentID) REFERENCES COMMENT(commentID) ON DELETE CASCADE,
    FOREIGN KEY (userID) REFERENCES USERS(userID) ON DELETE CASCADE
);

CREATE TABLE FOLLOW (
    followerID INTEGER NOT NULL,
    followeeID INTEGER NOT NULL,
    state TEXT NOT NULL CHECK (state IN ('Pending', 'Accepted', 'Rejected')),
    followDate TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CHECK (followerID <> followeeID),
    PRIMARY KEY (followerID, followeeID),
    FOREIGN KEY (followerID) REFERENCES USERS(userID) ON DELETE CASCADE,
    FOREIGN KEY (followeeID) REFERENCES USERS(userID) ON DELETE CASCADE
);

CREATE TABLE NOTIFICATION (
    notificationID SERIAL PRIMARY KEY,
    receiverID INTEGER NOT NULL,
    date TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    seen BOOLEAN NOT NULL,
    followID INTEGER DEFAULT NULL,
    commentID INTEGER DEFAULT NULL,
    likeID INTEGER DEFAULT NULL,
    CHECK (
        (followID IS NOT NULL AND commentID IS NULL AND likeID IS NULL) OR
        (followID IS NULL AND commentID IS NOT NULL AND likeID IS NULL) OR
        (followID IS NULL AND commentID IS NULL AND likeID IS NOT NULL)
    ),
    FOREIGN KEY (receiverID) REFERENCES USERS(userID) ON DELETE CASCADE,
    FOREIGN KEY (followID) REFERENCES USERS(USERID) ON DELETE CASCADE,
    FOREIGN KEY (commentID) REFERENCES COMMENT(commentID) ON DELETE CASCADE,
    FOREIGN KEY (likeID) REFERENCES LIKES(likeID) ON DELETE CASCADE,
    CHECK (followID <> receiverID)
);

CREATE TABLE BLOCK (
    blockerID INTEGER NOT NULL,
    blockedID INTEGER NOT NULL,
    CHECK (blockerID <> blockedID),
    PRIMARY KEY (blockerID, blockedID),
    FOREIGN KEY (blockerID) REFERENCES USERS(userID) ON DELETE CASCADE,
    FOREIGN KEY (blockedID) REFERENCES USERS(userID) ON DELETE CASCADE
);

CREATE TABLE USER_TOPICS (
    userID INTEGER NOT NULL,
    topicID INTEGER NOT NULL,
    PRIMARY KEY (userID, topicID),
    FOREIGN KEY (userID) REFERENCES USERS(userID) ON DELETE CASCADE,
    FOREIGN KEY (topicID) REFERENCES TOPIC(topicID) ON DELETE CASCADE
);

CREATE TABLE USER_REPORTS (
    reportID SERIAL PRIMARY KEY,
    userID INTEGER NOT NULL,
    postID INTEGER DEFAULT NULL,
    commentID INTEGER DEFAULT NULL,
    reason TEXT,
    search TSVECTOR,
    CHECK ((postID IS NOT NULL AND commentID IS NULL) OR (postID IS NULL AND commentID IS NOT NULL)),
    FOREIGN KEY (userID) REFERENCES USERS(userID) ON DELETE CASCADE,
    FOREIGN KEY (postID) REFERENCES POST(postID) ON DELETE CASCADE,
    FOREIGN KEY (commentID) REFERENCES COMMENT(commentID) ON DELETE CASCADE
);

CREATE TABLE GROUP_INVITATION (
    invitationID SERIAL PRIMARY KEY,
    groupID INTEGER NOT NULL,
    userID INTEGER NOT NULL,
    createdDate TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (groupID) REFERENCES GROUPS(groupID) ON DELETE CASCADE,
    FOREIGN KEY (userID) REFERENCES USERS(userID) ON DELETE CASCADE,
    UNIQUE (groupID, userID)
);

CREATE TABLE POST_TOPICS (
    postID INTEGER NOT NULL,
    topicID INTEGER NOT NULL,
    PRIMARY KEY (postID, topicID),
    FOREIGN KEY (postID) REFERENCES POST(postID) ON DELETE CASCADE,
    FOREIGN KEY (topicID) REFERENCES TOPIC(topicID) ON DELETE CASCADE
); 

------------------  PERFORMANCE INDEXES -----------------------------------------

CREATE INDEX postID_postTopics_idx ON POST_TOPICS USING hash (postID);

CREATE INDEX postID_comment_idx ON comment USING hash (postID);

CREATE INDEX receiver_idx ON notification USING hash (receiverID);

-------------------  FULL TEXT SEARCH -------------------------------------------

-- Create function to update the ts_vectors
CREATE OR REPLACE FUNCTION user_search_update() RETURNS TRIGGER AS $$ 
BEGIN 
IF TG_OP = 'INSERT' THEN 
    NEW.search = to_tsvector('english', NEW.userName); 
END IF; 
IF TG_OP = 'UPDATE' THEN 
IF NEW.userName <> OLD.userName THEN 
        NEW.search = to_tsvector('english', NEW.userName); 
END IF; 
END IF; 
RETURN NEW; 
END 
$$ LANGUAGE 'plpgsql';
--Create trigger to execute the td_vector function when the table is updated
CREATE TRIGGER user_search_update
BEFORE INSERT OR UPDATE ON Users
FOR EACH ROW
EXECUTE PROCEDURE user_search_update();
--Create the index for the ts_vectors
CREATE INDEX user_search ON Users USING GIN (search);


-- Create function to update the ts_vectors
CREATE OR REPLACE FUNCTION group_search_update() RETURNS TRIGGER AS $$ 
BEGIN 
IF TG_OP = 'INSERT' THEN 
    NEW.search = (setweight(to_tsvector('english', NEW.groupName),'A') || setweight  (to_tsvector('english', NEW.description),'B'));
END IF; 
IF TG_OP = 'UPDATE' THEN 
IF NEW.groupName <> OLD.groupName OR NEW.description <> OLD.description THEN 
        NEW.search = (setweight(to_tsvector('english', NEW.groupname),'A') || setweight  (to_tsvector('english', NEW.description),'B'));
END IF; 
END IF; 
RETURN NEW; 
END 
$$ LANGUAGE 'plpgsql';
--Create trigger to execute the td_vector function when the table is updated
CREATE TRIGGER group_search_update
BEFORE INSERT OR UPDATE ON Groups
FOR EACH ROW
EXECUTE PROCEDURE group_search_update();
--Create the index for the ts_vectors
CREATE INDEX group_search ON Groups USING GIN (search);


-- Create function to update the ts_vectors
CREATE OR REPLACE FUNCTION post_search_update() RETURNS TRIGGER AS $$ 
BEGIN 
IF TG_OP = 'INSERT' THEN 
    NEW.search = to_tsvector('english', NEW.message); 
END IF; 
IF TG_OP = 'UPDATE' THEN 
IF NEW.message <> OLD.message THEN 
        NEW.search = to_tsvector('english', NEW.message); 
END IF; 
END IF; 
RETURN NEW; 
END 
$$ LANGUAGE 'plpgsql';
--Create trigger to execute the td_vector function when the table is updated
CREATE TRIGGER post_search_update
BEFORE INSERT OR UPDATE ON Post
FOR EACH ROW
EXECUTE PROCEDURE post_search_update();
--Create the index for the ts_vectors
CREATE INDEX post_search ON Post USING GIN (search);


-- Create function to update the ts_vectors
CREATE OR REPLACE FUNCTION comment_search_update() RETURNS TRIGGER AS $$ 
BEGIN 
IF TG_OP = 'INSERT' THEN 
    NEW.search = to_tsvector('english', NEW.message); 
END IF; 
IF TG_OP = 'UPDATE' THEN 
IF NEW.message <> OLD.message THEN 
        NEW.search = to_tsvector('english', NEW.message); 
END IF; 
END IF; 
RETURN NEW; 
END 
$$ LANGUAGE 'plpgsql';
--Create trigger to execute the td_vector function when the table is updated
CREATE TRIGGER comment_search_update
BEFORE INSERT OR UPDATE ON Comment
FOR EACH ROW
EXECUTE PROCEDURE comment_search_update();
--Create the index for the ts_vectors
CREATE INDEX comment_search ON Topic USING GIN (search);


-- Create function to update the ts_vectors
CREATE OR REPLACE FUNCTION topic_search_update() RETURNS TRIGGER AS $$ 
BEGIN 
    IF TG_OP = 'INSERT' THEN 
        NEW.search := to_tsvector('english', NEW.topicName);  -- Change 'name' to 'topicName'
    
    ELSIF TG_OP = 'UPDATE' THEN 
        IF NEW.topicName <> OLD.topicName THEN  -- Change 'name' to 'topicName'
            NEW.search := to_tsvector('english', NEW.topicName);  -- Change 'name' to 'topicName'
        END IF; 
    END IF; 

    RETURN NEW; 
END 
$$ LANGUAGE 'plpgsql';
--Create trigger to execute the td_vector function when the table is updated
CREATE TRIGGER topic_search_update
BEFORE INSERT OR UPDATE ON Topic
FOR EACH ROW
EXECUTE PROCEDURE topic_search_update();
--Create the index for the ts_vectors
CREATE INDEX topic_search ON Topic USING GIN (search);

-- Create function to update the ts_vectors
CREATE OR REPLACE FUNCTION report_search_update() RETURNS TRIGGER AS $$ 
BEGIN 
    IF TG_OP = 'INSERT' THEN 
        NEW.search := to_tsvector('english', NEW.reason); 
    
    ELSIF TG_OP = 'UPDATE' THEN 
        IF NEW.reason <> OLD.reason THEN  
            NEW.search := to_tsvector('english', NEW.reason);  
        END IF; 
    END IF; 

    RETURN NEW; 
END 
$$ LANGUAGE 'plpgsql';
--Create trigger to execute the td_vector function when the table is updated
CREATE TRIGGER report_search_update
BEFORE INSERT OR UPDATE ON USER_REPORTS
FOR EACH ROW
EXECUTE PROCEDURE report_search_update();
--Create the index for the ts_vectors
CREATE INDEX report_search ON USER_REPORTS USING GIN (search);



CREATE OR REPLACE FUNCTION notify_content_owner_on_like()
RETURNS TRIGGER AS $$
DECLARE
    content_owner INTEGER;
BEGIN
    -- Check if the like is for a post and get the post owner
    IF NEW.postID IS NOT NULL THEN
        SELECT userID INTO content_owner FROM POST WHERE postID = NEW.postID;

        --Don't notify if like was done by post owner
        IF content_owner = NEW.userID THEN
            RETURN NEW;
        END IF;

    -- If the like is for a comment, get the comment owner
    ELSIF NEW.commentID IS NOT NULL THEN
        SELECT userID INTO content_owner FROM COMMENT WHERE commentID = NEW.commentID;

        --Don't notify if like was done by comment owner
        IF content_owner = NEW.userID THEN
            RETURN NEW;
        END IF;
    END IF;

    -- Insert the notification for the content owner
    INSERT INTO NOTIFICATION (receiverID, date, seen, likeID)
    VALUES (content_owner, NEW.createdDate, FALSE, NEW.likeID);

    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER notify_content_owner_on_like  -- (TRIGGER 01)
AFTER INSERT ON LIKES
FOR EACH ROW
EXECUTE FUNCTION notify_content_owner_on_like();


CREATE OR REPLACE FUNCTION notify_content_owner_on_comment()
RETURNS TRIGGER AS $$
DECLARE
    content_owner INTEGER;
BEGIN
    -- Check if the comment is for a post and get the post owner
    IF NEW.postID IS NOT NULL THEN
        SELECT userID INTO content_owner FROM POST WHERE postID = NEW.postID;

        --Don't notify if comment was made by post owner
        IF content_owner = NEW.userID THEN
            RETURN NEW;
        END IF;

    -- If the like is for a comment, get the comment owner
    ELSIF NEW.parentCommentID IS NOT NULL THEN
        SELECT userID INTO content_owner FROM COMMENT WHERE commentID = NEW.parentCommentID;

        --Don't notify if comment was made by comment owner
        IF content_owner = NEW.userID THEN
            RETURN NEW;
        END IF;
    END IF;

    -- Insert the notification for the content owner
    INSERT INTO NOTIFICATION (receiverID, date, seen, commentID)
    VALUES (content_owner, NEW.createdDate, FALSE, NEW.commentID);

    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

CREATE TRIGGER notify_content_owner_on_comment -- (TRIGGER 02)
AFTER INSERT ON COMMENT
FOR EACH ROW
EXECUTE FUNCTION notify_content_owner_on_comment();



CREATE OR REPLACE FUNCTION notify_user_on_follow()
RETURNS TRIGGER AS $$
BEGIN
    -- Insert a notification for the user being followed
    INSERT INTO NOTIFICATION (receiverID, date, seen, followID)
    VALUES (NEW.followeeID, NEW.followDate, FALSE, NEW.followerID);

    RETURN NEW;
END;
$$ LANGUAGE plpgsql;


CREATE TRIGGER notify_user_on_follow -- (TRIGGER 03)
AFTER INSERT ON FOLLOW
FOR EACH ROW
EXECUTE FUNCTION notify_user_on_follow();


--Create function to verify that an user only post on groups that he is in
CREATE OR REPLACE FUNCTION verify_group_posts() RETURNS TRIGGER AS $$
BEGIN 
    IF NEW.groupID IS NULL THEN
        RETURN NEW; 
    END IF;
    IF NOT EXISTS (SELECT 1 FROM GROUP_MEMBERSHIP WHERE NEW.userID = GROUP_MEMBERSHIP.userID AND NEW.groupID = GROUP_MEMBERSHIP.groupID AND NEW.groupID IS NOT NULL)
    THEN 
		RAISE EXCEPTION 'A user can only post on groups to which they belong';
    END IF;
	RETURN NEW;
END
$$ LANGUAGE 'plpgsql';

--Create trigger to apply the verify_group_posts() function when the table is updated (TRIGGER 04)
CREATE TRIGGER verify_group_posts
    BEFORE INSERT OR UPDATE ON POST
    FOR EACH ROW
    EXECUTE PROCEDURE verify_group_posts();




--Create function to verify that an user only comment on groups that he is in
CREATE OR REPLACE FUNCTION verify_group_post_comments() RETURNS TRIGGER AS $$
BEGIN 
    IF EXISTS (SELECT 1 FROM POST WHERE NEW.postID = POST.postID AND POST.groupID IS NOT NULL)
    AND NOT EXISTS (SELECT 1 FROM GROUP_MEMBERSHIP WHERE GROUP_MEMBERSHIP.groupID = (SELECT groupID FROM POST WHERE postID = NEW.postID) AND GROUP_MEMBERSHIP.userID = NEW.userID) 
	THEN
		RAISE EXCEPTION 'A user can only comment on posts from groups to which they belong';
    END IF;
	RETURN NEW;
END
$$ LANGUAGE 'plpgsql';

--Create trigger to apply the verify_group_post_comments() function when the table is updated (TRIGGER 05)
CREATE TRIGGER verify_group_post_comments 
    BEFORE INSERT OR UPDATE ON COMMENT
    FOR EACH ROW
    EXECUTE PROCEDURE verify_group_post_comments();



--Create function to verify that an user only likes group posts if he is in that group
CREATE OR REPLACE FUNCTION verify_group_post_likes() RETURNS TRIGGER AS $$
BEGIN 
    IF EXISTS (SELECT 1 FROM POST WHERE NEW.postID = postID AND groupID IS NOT NULL)
    AND NOT EXISTS (SELECT 1 FROM GROUP_MEMBERSHIP WHERE GROUP_MEMBERSHIP.groupID = (SELECT groupID FROM POST WHERE postID = NEW.postID) AND GROUP_MEMBERSHIP.userID = NEW.userID) 
	THEN
    	RAISE EXCEPTION 'A user can only like group posts if he belongs to that group';
    END IF;
RETURN NEW;
END
$$ LANGUAGE 'plpgsql';

--Create trigger to apply the verify_group_post_likes() function when the table is updated (TRIGGER 06)
CREATE TRIGGER verify_group_post_likes 
    BEFORE INSERT OR UPDATE ON LIKES
    FOR EACH ROW
    EXECUTE PROCEDURE verify_group_post_likes();

--Create function to verify that an user can only like a post once
CREATE OR REPLACE FUNCTION verify_post_likes() RETURNS TRIGGER AS $$
BEGIN
    IF EXISTS (SELECT * FROM LIKES WHERE NEW.userID = userID AND NEW.postID = postID AND postID IS NOT NULL) THEN
    RAISE EXCEPTION 'A user can only like a post once';
    END IF;
	RETURN NEW;
END
$$ LANGUAGE 'plpgsql';

--Create trigger to apply the verify_post_likes() function when the table is updated (TRIGGER 07)
CREATE TRIGGER verify_post_likes 
    BEFORE INSERT OR UPDATE ON LIKES
    FOR EACH ROW
    EXECUTE PROCEDURE verify_post_likes();


--Create function to verify that an user can only like a comment once
CREATE OR REPLACE FUNCTION verify_comment_likes() RETURNS TRIGGER AS $$
BEGIN
    IF EXISTS (SELECT * FROM LIKES WHERE NEW.userID = userID AND NEW.commentID = commentID AND commentID IS NOT NULL) THEN
    RAISE EXCEPTION 'A user can only like a comment once';
    END IF;
    RETURN NEW;
END
$$ LANGUAGE 'plpgsql';

--Create trigger to apply the verify_comment_likes() function when the table is updated (TRIGGER 08)
CREATE TRIGGER verify_comment_likes 
    BEFORE INSERT OR UPDATE ON LIKES
    FOR EACH ROW
    EXECUTE PROCEDURE verify_comment_likes();


--Create function to verify that an user cannot request to join if they are already member of that group
CREATE OR REPLACE FUNCTION verify_group_join_request() RETURNS TRIGGER AS $$
BEGIN 
    IF EXISTS (
        SELECT 1 
        FROM GROUP_MEMBERSHIP 
        WHERE userID = NEW.userID AND groupID = NEW.groupID
    ) THEN
        RAISE EXCEPTION 'A user cannot request to join a group that he is already a member of';
    END IF;
    RETURN NEW;
END;
$$ LANGUAGE 'plpgsql';

--Create trigger to apply the verify_group_join_request() function when the table is updated (TRIGGER 09)
CREATE TRIGGER verify_group_join_request
    BEFORE INSERT OR UPDATE ON JOIN_GROUP_REQUEST
    FOR EACH ROW
    EXECUTE PROCEDURE verify_group_join_request();


-- Create function to verify that a comment date is equal to or greater than the post creation date
CREATE OR REPLACE FUNCTION verify_comment_date() RETURNS TRIGGER AS $$
BEGIN 
    IF NEW.postID IS NOT NULL THEN
        IF NOT EXISTS (SELECT 1 FROM POST WHERE postID = NEW.postID) THEN
            RAISE EXCEPTION 'Post with ID % does not exist', NEW.postID;
        END IF;

        IF NEW.createdDate < (SELECT createdDate FROM POST WHERE postID = NEW.postID) THEN
            RAISE EXCEPTION 'A comment date must be equal to or greater than the post creation date';
        END IF;
    END IF;

    RETURN NEW;
END;
$$ LANGUAGE plpgsql;


-- Create trigger to apply the verify_comment_date() function when the COMMENT table is updated (TRIGGER 10)
CREATE TRIGGER verify_comment_date
    BEFORE INSERT OR UPDATE ON COMMENT
    FOR EACH ROW
    EXECUTE FUNCTION verify_comment_date();


-- Create function to verify that a like date is equal to or greater than the post creation date
CREATE OR REPLACE FUNCTION verify_like_post_date() RETURNS TRIGGER AS $$
BEGIN 
    IF NEW.postID IS NOT NULL THEN
        IF NEW.createdDate < (SELECT createdDate FROM POST WHERE postID = NEW.postID) THEN
            RAISE EXCEPTION 'A like date must be equal to or greater than the post creation date';
        END IF;
    END IF;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

-- Create trigger to apply the verify_like_post_date() function when the LIKES table is updated (TRIGGER 11)
CREATE TRIGGER verify_like_post_date
    BEFORE INSERT OR UPDATE ON LIKES
    FOR EACH ROW
    EXECUTE FUNCTION verify_like_post_date();


-- Create function to verify that a like date is equal to or greater than the comment creation date
CREATE OR REPLACE FUNCTION verify_like_comment_date() RETURNS TRIGGER AS $$
BEGIN 
    IF NEW.commentID IS NOT NULL THEN
        IF NEW.createdDate < (SELECT createdDate FROM COMMENT WHERE commentID = NEW.commentID) THEN
            RAISE EXCEPTION 'A like date must be equal to or greater than the comment creation date';
        END IF;
    END IF;

    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

-- Create trigger to apply the verify_like_comment_date() function when the LIKES table is updated (TRIGGER 12)
CREATE TRIGGER verify_like_comment_date
    BEFORE INSERT OR UPDATE ON LIKES
    FOR EACH ROW
    EXECUTE FUNCTION verify_like_comment_date();


-- Create function to verify that a reply comment date is equal to or greater than the original comment creation date
CREATE OR REPLACE FUNCTION verify_reply_comment_date() 
RETURNS TRIGGER AS $$
BEGIN 
    IF NEW.parentCommentID IS NOT NULL THEN
        IF NOT EXISTS (SELECT 1 FROM COMMENT WHERE commentID = NEW.parentCommentID) THEN
            RAISE EXCEPTION 'Parent comment not found for parentCommentID: %', NEW.parentCommentID;
        END IF;

        IF NEW.createdDate < (SELECT createdDate FROM COMMENT WHERE commentID = NEW.parentCommentID) THEN
            RAISE EXCEPTION 'A reply comment date must be equal to or greater than the original comment creation date';
        END IF;
    END IF;

    RETURN NEW;
END;
$$ LANGUAGE plpgsql; 


-- Create trigger to apply the verify_reply_comment_date() function when the COMMENT table is updated (TRIGGER 13)
CREATE TRIGGER verify_reply_comment_date
    BEFORE INSERT OR UPDATE ON COMMENT
    FOR EACH ROW
    EXECUTE FUNCTION verify_reply_comment_date();


-- Create function to verify that a group owner only invites users who are not already in that group
CREATE OR REPLACE FUNCTION verify_group_owner_invites() RETURNS TRIGGER AS $$
BEGIN 
    IF EXISTS (SELECT * FROM GROUP_MEMBERSHIP WHERE NEW.userID = GROUP_MEMBERSHIP.userID AND NEW.groupID = GROUP_MEMBERSHIP.groupID) THEN
        RAISE EXCEPTION 'A user can only be invited to a group they are not already in';
    END IF;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

-- Create trigger to apply the verify_group_owner_invites() function when the GROUP_INVITATION table is updated (TRIGGER 14)
CREATE TRIGGER verify_group_owner_invites 
    BEFORE INSERT OR UPDATE ON GROUP_INVITATION
    FOR EACH ROW
    EXECUTE FUNCTION verify_group_owner_invites();


-- Create function to enforce unique reports per user per post or comment
CREATE OR REPLACE FUNCTION verify_unique_report() RETURNS TRIGGER AS $$
BEGIN
    IF EXISTS (
        SELECT 1
        FROM USER_REPORTS
        WHERE userID = NEW.userID
        AND (
            (postID IS NOT DISTINCT FROM NEW.postID AND commentID IS NULL)
            OR 
            (commentID IS NOT DISTINCT FROM NEW.commentID AND postID IS NULL)
        )
    ) THEN
        RAISE EXCEPTION 'User has already reported this post or comment.';
    END IF;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

-- Trigger to call the verify_unique_report function before inserting a new report (TRIGGER 15)
CREATE TRIGGER verify_unique_report
BEFORE INSERT ON USER_REPORTS
FOR EACH ROW
EXECUTE FUNCTION verify_unique_report();


-- Create function to verify that a user does not report their own posts
CREATE OR REPLACE FUNCTION verify_user_post_reports() RETURNS TRIGGER AS $$
BEGIN 
    IF EXISTS (SELECT 1 FROM POST WHERE NEW.userID = POST.userID AND NEW.postID = POST.postID) THEN
        RAISE EXCEPTION 'A user cannot report their own posts';
    END IF;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

-- Create trigger to apply the verify_user_post_reports() function when the USER_REPORTS table is updated (TRIGGER 16)
CREATE TRIGGER verify_user_post_reports
    BEFORE INSERT OR UPDATE ON USER_REPORTS
    FOR EACH ROW
    EXECUTE FUNCTION verify_user_post_reports();


-- Create function to verify that a user does not report their own comments
CREATE OR REPLACE FUNCTION verify_user_comment_reports() RETURNS TRIGGER AS $$
BEGIN 
    IF EXISTS (SELECT 1 FROM COMMENT WHERE NEW.userID = COMMENT.userID AND NEW.commentID = COMMENT.commentID) THEN
        RAISE EXCEPTION 'A user cannot report their own comments';
    END IF;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

-- Create trigger to apply the verify_user_comment_reports() function when the USER_REPORTS table is updated (TRIGGER 17)
CREATE TRIGGER verify_user_comment_reports
    BEFORE INSERT OR UPDATE ON USER_REPORTS
    FOR EACH ROW
    EXECUTE FUNCTION verify_user_comment_reports();


-- Create function to prevent admin users from posting, liking, or commenting
CREATE OR REPLACE FUNCTION prevent_admin_actions()
RETURNS TRIGGER AS $$
BEGIN
    IF (SELECT isAdmin FROM USERS WHERE userID = NEW.userID) THEN
        RAISE EXCEPTION 'Admin users are not allowed to post, like, or comment.';
    END IF;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

-- Trigger to prevent admin users from creating a post (TRIGGER 18)
CREATE TRIGGER prevent_admin_actions_post
BEFORE INSERT ON POST
FOR EACH ROW
EXECUTE FUNCTION prevent_admin_actions();

-- Trigger to prevent admin users from creating a comment (TRIGGER 19)
CREATE TRIGGER prevent_admin_actions_comment
BEFORE INSERT ON COMMENT
FOR EACH ROW
EXECUTE FUNCTION prevent_admin_actions();

-- Trigger to prevent admin users from liking a post or comment (TRIGGER 19)
CREATE TRIGGER prevent_admin_actions_like
BEFORE INSERT ON LIKES
FOR EACH ROW
EXECUTE FUNCTION prevent_admin_actions();


-- Create function to prevent admin users from following other users (TRIGGER 20)
CREATE OR REPLACE FUNCTION prevent_admin_actions_follow()
RETURNS TRIGGER AS $$
BEGIN
    IF (SELECT isAdmin FROM USERS WHERE userID = NEW.followerID) THEN
        RAISE EXCEPTION 'Admin users are not allowed to follow other users.';
    END IF;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

-- Trigger to prevent admin users from creating a post (TRIGGER 20)
CREATE TRIGGER prevent_admin_actions_follow
BEFORE INSERT ON FOLLOW
FOR EACH ROW
EXECUTE FUNCTION prevent_admin_actions_follow();

-- Create function to delete rejected follow requests (TRIGGER 21)
CREATE OR REPLACE FUNCTION delete_rejected_follow_request()
RETURNS TRIGGER AS $$
BEGIN
    -- Check if the follow state is updated to 'rejected'
    IF NEW.state = 'rejected' THEN
        DELETE FROM follow WHERE id = NEW.id;
    END IF;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

-- (TRIGGER 21)
CREATE TRIGGER delete_rejected_follow  
AFTER UPDATE ON follow
FOR EACH ROW
EXECUTE FUNCTION delete_rejected_follow_request();

-- Function to assign the general topic to a post if it has no topics (TRIGGER 22)
CREATE OR REPLACE FUNCTION assign_general_topic_to_post()
RETURNS TRIGGER AS $$
BEGIN
    -- Check if the post has no associated topics
    IF NOT EXISTS (
        SELECT 1
        FROM post_topics
        WHERE postID = NEW.postID
    ) THEN
        -- Insert the general topic for the post
        INSERT INTO post_topics (postID, topicID)
        VALUES (NEW.postID, 1); --1 is the id of general topic
    END IF;

    -- Detach general topic if it is no longer relevant
    IF EXISTS (
        SELECT 1
        FROM post_topics
        WHERE postID = NEW.postID AND topicID = 1
    ) AND NOT EXISTS (
        SELECT 1
        FROM post_topics
        WHERE postID = NEW.postID AND topicID != 1
    ) THEN
        DELETE FROM post_topics WHERE postID = NEW.postID AND topicID = 1;
    END IF;

    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

-- Trigger to invoke the function after a post is inserted (TRIGGER 22)
CREATE TRIGGER add_general_topic_to_post
AFTER INSERT ON post    
FOR EACH ROW
EXECUTE FUNCTION assign_general_topic_to_post();



--------------- POPULATE DATABASE ---------------------------------

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
    (2, 'Just returned from an amazing hike in the mountains.', TRUE, '2024-12-28 10:30:00', NULL), 
    (3, 'Here’s my latest painting, inspired by nature!', TRUE, '2024-12-28 14:00:00', NULL),
    (4, 'Check out this delicious recipe I just made!', TRUE, '2024-12-28 09:15:00', NULL),
    (5, 'Just visited a new country, and it was incredible!', FALSE, '2024-12-28 18:45:00', NULL),
    (6, 'Had a great day coaching my team today!', TRUE, '2024-12-28 12:20:00', NULL),
    (7, 'Petition to make check in at hotels 11am and check out 3pm not over way round wtfffff', TRUE, '2024-12-28 07:30:00', NULL), 
    (8, 'Can’t wait for my vegetables to be ready for harvest!', FALSE, '2024-12-28 16:40:00', 7), 
    (9, 'Currently working on my novel, it’s coming together!', TRUE, '2024-12-28 20:05:00', NULL), 
    (10, 'Played at a local café last night, had a blast!', FALSE, '2024-12-28 21:15:00', NULL),
    (5, 'Just completed a challenging workout—feeling accomplished!', TRUE, '2024-12-28 18:15:00', NULL),
    (2, 'Exploring new music genres to expand my playlist!', TRUE, '2024-12-28 20:45:00', NULL),
    (3, 'What’s everyone’s favorite movie from the 90s?', TRUE, '2024-12-28 22:00:00', NULL),
    (4, 'Volunteered at the local animal shelter today—such a rewarding experience!', TRUE, '2024-12-28 17:30:00', NULL),
    (5, 'Who else is getting excited for the upcoming holiday season?', FALSE, '2024-12-28 12:00:00', NULL),
    (6, 'Working on my coding skills—any recommendations for resources?', TRUE, '2024-12-28 09:45:00', NULL),
    (7, 'Just finished a beautiful sunset painting!', TRUE, '2024-12-28 19:00:00', NULL),
    (8, 'Mewing tournament today at 3pm, do not miss', TRUE, '2024-12-28 14:30:00', NULL),
    (9, 'Had a fantastic time at the local food festival last night!', TRUE, '2024-12-28 20:00:00', NULL),
    (3, 'Looking for tips on traveling solo—any experiences to share?', TRUE, '2024-12-28 15:00:00', NULL),
    (11, 'Based on Fabrizio’s latest comments about Spain, it looks like Rashford’s ideal destination is Barcelona. I also think Arsenal/Chelsea will be interested in January, especially with Martinelli and Sterling’s poor season and Mudryk’s recent suspension from football. Where do you guys think he’ll go?', TRUE, '2024-12-28 12:00:00', NULL),
    (11, '🚨 - Rúben Amorim’s latest press conference had ZERO questions about the game against Spurs tomorrow. Instead, there were 11 questions about Marcus Rashford.', TRUE, '2024-12-28 00:00:00', NULL),
    (11, '🚨🗣️ - Rúben Amorim: "My goal is to set STANDARDS, see if the players are able to meet them, and then prepare for the matches. I am focused on that."', TRUE, '2024-12-28 00:05:00', NULL),
    (11, '“Really good. Trained really well. He seems a little bit upset with me and that is perfect. I was really, really happy because I would do the same and he is ready for this game.”', TRUE, '2024-12-28 00:10:00', NULL),
    (7, 'I don’t understand the concept of people turning to facebook for medical advice especially for their children wtf', TRUE, '2024-12-28 10:05:00', NULL),
    (2, 'would you guys still love taylor if she was a worm #taylorpede', TRUE, '2024-12-28 10:05:00', 9), 
    (10, 'I drew Taylor to a T! :D #terrific', TRUE, NOW(), 9), 
    (10, 'Taylor Swift''s Wins Grammy!', TRUE, NOW(), 9),
    (11, 'Welcome, we can freely discuss everything related to United here.', TRUE, '2024-12-28 09:00:00', 10),
    (11, 'What a performance by Rashford last night! Truly world-class.', TRUE, '2024-12-28 10:00:00', 10),
    (11, 'We really need reinforcements in defense this January. Thoughts?', TRUE, '2024-12-28 12:30:00', 10),
    (6, 'Ruben is changing the culture!!', TRUE, '2024-12-28 14:00:00', 10),
    (7, 'Looking forward to the next match! Hoping to see more from Garnacho.', TRUE, '2024-12-28 16:45:00', 10),
    (4, 'Ruben’s tactics are starting to really show their worth. What do you guys think?', TRUE, '2024-12-28 18:00:00', 10),
    (5, 'Does anyone else think that Bruno deserves more credit for his leadership?', TRUE, '2024-12-28 19:30:00', 10),
    (6, 'What’s up with City?', TRUE, '2024-12-28 21:00:00', 10),
    (7, 'How amazing was that Casemiro header? Pure class!', TRUE, '2024-12-28 09:00:00', 10),
    (8, 'Love this man!', TRUE, '2024-12-28 11:00:00', 10),
    (9, 'What’s everyone’s prediction for the game against Arsenal?', TRUE, '2024-12-28 13:00:00', 10),
    (10, 'Not sure if there are any sports cards collectors here. But I just picked this up. Couldn’t be happier. My current favorite player on our team ❤️ ', TRUE, '2024-12-28 15:00:00', 10),
    (3, 'Visited an amazing art gallery today! So inspired to create something new.', TRUE, '2024-12-28 18:41:54', NULL),
    (4, 'Just cooked up a new recipe: spicy truffle pasta! Who wants the recipe?', TRUE, '2024-12-28 18:46:54', NULL),
    (12, 'Training camp is going strong! Feeling ready for the next challenge. #UFC #Champion', TRUE, '2024-12-28 18:51:54', NULL);

INSERT INTO COMMENT (userID, message, createdDate, postID, parentCommentID) 
VALUES 
    (3, 'That looks absolutely delicious! Would you recommend it for a beginner cook? 🍳😊', '2024-12-29 15:00:00', 3, NULL),
    (4, 'Where did you go?', '2024-12-29 15:00:00', 4, NULL),
    (3, 'Sounds incredible! Nothing beats the fresh mountain air and those breathtaking views. 🏔️🌲', '2025-01-14 05:00:00', 1, NULL),
    (2, 'I loved the view from the top of the mountain!', '2025-01-14 15:01:00', NULL, 3),
    (2, 'That looks mouthwatering! 🍽️ Care to share the recipe? I’d love to try making it!', '2024-12-29 15:05:00', 3, NULL),
    (5, 'That sounds delicious! What ingredients did you use?', NOW(), 3, NULL),
    (7, 'Sounds like a great hike! Where did you go?', NOW(), 1, NULL),
    (6, 'Can’t wait to read your novel! What’s it about?', NOW(), 8, NULL),
    (8, 'That’s amazing! Writing a novel is no small feat—keep the creativity flowing! 📚✨', NOW(), 8, NULL),
    (3, 'I’d love to join you for a hike sometime!', NOW(), 1, NULL),
    (10, 'Great job pushing through the challenge! Keep up the awesome work—you’re inspiring! 💪🔥', NOW(), 10, NULL),
    (9, 'We should play together sometime.', NOW(), 9, NULL),
    (10, 'Girl Math', NOW(), 6, NULL),
    (4, 'That’s awesome! Coaching is such a rewarding experience—your team is lucky to have you! 💪🏆', NOW(), 5, NULL),
    (5, 'Great to see you volunteering! Those animals need love!', NOW(), 13, NULL),
    (6, 'I’m also looking for solo travel tips! Let’s exchange ideas.', NOW(), 19, NULL),
    (8, 'That’s awesome! A sunset painting must look stunning. Can’t wait to see it!', NOW(), 16, NULL),
    (7, 'I will be there, no matter what!', NOW(), 17, NULL),
    (9, 'Food festivals are the best! Have you tried them?', NOW(), 19, NULL),
    (10, 'That’s amazing! I’d love to see it. There’s something so calming about sunset colors. What medium did you use for the painting?', NOW(), 16, NULL),
    (4, 'That’s awesome! I’d recommend checking out sites like FreeCodeCamp for structured courses and projects, or LeetCode for practicing algorithms. Also, don’t forget about Stack Overflow for any coding questions you might have. Keep it up!', NOW(), 15, NULL),
    (2, 'He doesn’t start ahead of Lamine or Raphina & Barca can’t afford him anyway.', '2024-12-29 15:00:00', 20, NULL),  
    (4, 'Rashford will cook like crazy at Barcelona lbr 😭', '2024-12-29 15:01:00', 20, NULL),  
    (5, 'Considering his wages only Chelsea, PSG and Saudi seems appropriate. Maybe Newcastle too', '2024-12-29 15:02:00', 20, NULL),  
    (6, 'Hola, soy Marcos Rashford', '2024-12-29 15:03:00', 20, NULL),  
    (7, 'We shouldn’t be selling him to premier league rivals who are challenging for the title.', '2024-12-29 15:04:00', 20, NULL),
    (6, 'Atletico maybe?', '2024-12-29 17:26:00', NULL, 22),
    (7, 'how sweet! 🥰🥰😍', '2024-12-29 8:26:00', 25, NULL),
    
    /* comment id 29*/(2, 'Rashford was incredible! That goal will be remembered for ages.', '2024-12-29 10:30:00', 29, NULL),
    (3, 'Absolutely agree! He’s been our standout player this season.', '2024-12-29 10:45:00', 29, NULL),
    (4, 'The chemistry between Rashford and Bruno is unreal!', '2024-12-29 11:00:00', 29, NULL),
    (5, 'Let’s hope he keeps this form up!', '2024-12-29 11:15:00', 29, NULL),
    (6, 'We desperately need a center-back. Who should we go for?', '2024-12-29 12:45:00', 30, NULL),
    (7, 'I think Timber would be a great addition.', '2024-12-29 13:00:00', 30, NULL),
    (8, 'Maybe we should try for Pau Torres again?', '2024-12-29 13:15:00', 30, NULL),
    (9, 'We also need a solid backup for Dalot.', '2024-12-29 13:30:00', 30, NULL),
    (10, 'Last weekend’s win was a testament to Ruben’s brilliance!', '2024-12-29 14:15:00', 31, NULL),
    (11, 'Agreed! The team really showed character.', '2024-12-29 14:30:00', 31, NULL),
    (2, 'Still can’t believe that comeback!', '2024-12-29 14:45:00', 31, NULL),
    (3, 'I’m so proud of this team!', '2024-12-29 15:00:00', 31, NULL),
    (4, 'Garnacho has so much potential! Hope he gets more minutes.', '2024-12-29 17:00:00', 32, NULL),
    (5, 'He’s a future star for sure.', '2024-12-29 17:15:00', 32, NULL),
    (6, 'Can’t wait to see him play in the next match!', '2024-12-29 17:30:00', 32, NULL),
    (7, 'His dribbling is insane for his age.', '2024-12-29 17:45:00', 32, NULL),
    (8, 'Ruben has transformed this team. Tactics on point!', '2024-12-29 18:15:00', 33, NULL),
    (9, 'His substitutions have been brilliant too.', '2024-12-29 18:30:00', 33, NULL),
    (10, 'Finally, we have a manager who understands the club.', '2024-12-29 18:45:00', 33, NULL),
    (11, 'Let’s hope he brings us some trophies soon!', '2024-12-29 19:00:00', 33, NULL),
    (2, 'Old Trafford is a fortress!', '2024-12-29 12:00:00', 34, NULL),
    (3, 'Don’t ask questions. Just drink the tears of all 5 of their fans LMAO', '2024-12-29 12:15:00', 35, NULL),
    (4, 'We need to strengthen our midfield depth.', '2024-12-29 13:30:00', 36, NULL),
    (2, 'That header was pure class! Casemiro is a beast in the air.', '2024-12-29 09:15:00', 36, NULL),
    (3, 'His experience really shines in moments like that.', '2024-12-29 09:30:00', 36, NULL),
    (4, 'We needed that goal so badly. What a player!', '2024-12-29 09:45:00', 36, NULL),
    (5, 'Casemiro’s leadership is unmatched. He’s so reliable.', '2024-12-29 10:00:00', 36, NULL),
    (6, 'Ruben is the best thing that happened to this team during these terrible years without Fergie ♥️♥️♥️', '2024-12-29 11:15:00', 37, NULL),
    (7, 'The genuine excitement of winning a derby in this fashion is probably one of the greatest feelings from either side in any derby.', '2024-12-29 11:30:00', 37, NULL),
    (8, 'Casemiro lol', NOW(), 37, NULL),
    (9, 'I think we’ll win 2-1. Rashford and Bruno to score.', '2024-12-29 13:15:00', 38, NULL),
    (10, 'This will be a tough game, but I believe in our squad.', '2024-12-29 13:30:00', 38, NULL),
    (2, 'Arsenal is strong, but we can counter them effectively.', '2024-12-29 13:45:00', 38, NULL),
    (3, 'I’m predicting a clean sheet for Onana. 1-0 United!', '2024-12-29 14:00:00', 38, NULL),
    (4, 'As long as we stay compact defensively, we’ll win.', '2024-12-29 14:15:00', 38, NULL),
    (5, 'Looking forward to Bruno bossing the midfield.', '2024-12-29 14:30:00', 38, NULL),
    (6, 'This squad has so much potential. We’re just getting started!', '2024-12-29 15:15:00', 39, NULL),
    (7, 'ETH’s biggest trick was to make us believe we were never even in the UCL last season.', '2024-12-29 15:30:00', 39, NULL),
    (8, 'Get it PSA graded if you’re in the US!', '2024-12-29 15:45:00', 39, NULL),
    (9, 'i used to be a card collector and my best purchase was a signed ogs card that is still at the front of my folder today ', '2024-12-29 16:00:00', 39, NULL),
    (10, 'Im in America and baseball cards are big business. I didn’t know Topps finest made soccer cards. That’s a beauty ', '2024-12-29 16:15:00', 39, NULL),
    (2, 'I’ve been saying for a while the Hojlund will thrive under Amorim - could be worth a pretty penny in time to come.', '2024-12-29 16:30:00', 39, NULL),
    (3, 'I love Rasmus as well, no need for a new striker, invest the money in lcb/lwb/lcam, our left side is lacking compared to the right', NOW(), 39, NULL),
    (4, 'We were in the champions league last season? How much did I drink to forget that? ', NOW(), 39, NULL),
    (2, 'I agree, Barcelona seems like a great fit for Rashford. They need a player like him.', '2024-12-29 17:20:00', 20, NULL),
    (3, 'Arsenal or Chelsea could really use him, but their poor seasons might hurt their chances.', '2024-12-29 17:25:00', 20, NULL),
    (4, 'I think Barcelona will make a strong push, especially with their financial troubles. They’ll need a player like Rashford.', '2024-12-29 17:30:00', 20, NULL),
    (5, 'Seems like Rashford is stealing the spotlight! That''s a lot of focus for one player before a big game.', '2024-12-29 18:00:00', 21, NULL),
    (6, 'Rashford''s future is certainly the talk of the town. Wonder if it''ll distract from the Spurs game?', '2024-12-29 17:21:00', 21, NULL),
    (7, 'Amorim must''ve been frustrated with all those questions! The game against Spurs should be the focus right now.', '2024-12-29 18:00:00', 21, NULL),
    (5, 'That dish looks amazing! What ingredients did you use?', NOW(), 41, NULL),
    (6, 'I need to try this recipe! Can you share it?', NOW(), 41, NULL),
    (7, 'Amazing fight, champ! You’re an inspiration.', NOW(), 42, NULL),
    (8, 'When’s the next title defense? Can’t wait to watch!', NOW(), 42, NULL),
    (9, 'What’s your favorite post-fight meal, Jonny?', NOW(), 42, NULL),
    (10, 'Your art is so inspiring, Charlie! What medium did you use?', NOW(), 2, NULL),
    (11, 'I’d love to see more of your pieces. Are they displayed somewhere?', NOW(), 2, NULL),
    (2, 'This is so creative! Can you share the inspiration behind it?', NOW(), 2, NULL),
    (6, 'Your gallery visits always seem exciting! Any recommendations?', NOW(), 40, NULL);

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
    (7, '2025-01-15 00:01:00', NULL, 3),   
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
    (11, '2025-01-15 00:01:00', NULL, 3),
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

    (3, '2024-12-29 18:25:00', 21, NULL),
    (4, '2024-12-29 18:30:00', 22, NULL),
    (5, '2024-12-29 18:35:00', 23, NULL),
    (7, '2024-12-29 18:45:00', 21, NULL),
    (8, '2024-12-29 18:50:00', 22, NULL),
    (9, '2024-12-29 18:55:00', 23, NULL),
    (11, '2024-12-29 19:05:00', 21, NULL);


        

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
    (39, 13),
    (25, 1),
    (26, 1),
    (27, 1),
    (40, 6),
    (21, 13),
    (24, 1),
    (42, 7),
    (41, 5), 
    (23, 13), 
    (22, 14);



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
