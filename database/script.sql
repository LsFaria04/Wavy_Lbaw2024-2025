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

-- Trigger to prevent admin users from creating a comment (TRIGGER 18)
CREATE TRIGGER prevent_admin_actions_comment
BEFORE INSERT ON COMMENT
FOR EACH ROW
EXECUTE FUNCTION prevent_admin_actions();

-- Trigger to prevent admin users from liking a post or comment (TRIGGER 18)
CREATE TRIGGER prevent_admin_actions_like
BEFORE INSERT ON LIKES
FOR EACH ROW
EXECUTE FUNCTION prevent_admin_actions();


-- Create function to prevent admin users from following other users
CREATE OR REPLACE FUNCTION prevent_admin_actions_follow()
RETURNS TRIGGER AS $$
BEGIN
    IF (SELECT isAdmin FROM USERS WHERE userID = NEW.followerID) THEN
        RAISE EXCEPTION 'Admin users are not allowed to follow other users.';
    END IF;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

-- Trigger to prevent admin users from creating a post (TRIGGER 18)
CREATE TRIGGER prevent_admin_actions_follow
BEFORE INSERT ON FOLLOW
FOR EACH ROW
EXECUTE FUNCTION prevent_admin_actions_follow();

-- Create function to delete rejected follow requests
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

CREATE TRIGGER delete_rejected_follow
AFTER UPDATE ON follow
FOR EACH ROW
EXECUTE FUNCTION delete_rejected_follow_request();


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
    (7, 'how sweet! 🥰🥰😍', '2024-12-18 17:26:00', NULL, 25); 

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
