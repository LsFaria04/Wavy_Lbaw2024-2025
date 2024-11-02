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
    userName VARCHAR(30) UNIQUE NOT NULL,
    passwordHash TEXT NOT NULL,
    bio TEXT,
    email TEXT UNIQUE NOT NULL,
    state TEXT NOT NULL CHECK (state IN ('active', 'suspended', 'deleted')),
    visibilityPublic BOOLEAN NOT NULL,
    isAdmin BOOLEAN NOT NULL
);

--Add column with the pre computed ts_vectors
ALTER TABLE Users ADD COLUMN search TSVECTOR;

CREATE TABLE MESSAGE (
    messageID SERIAL PRIMARY KEY,
    receiverID INTEGER NOT NULL,
    senderID INTEGER NOT NULL,
    message TEXT NOT NULL,
    date TIMESTAMP NOT NULL,
    FOREIGN KEY (receiverID) REFERENCES USERS(userID) ON DELETE CASCADE,
    FOREIGN KEY (senderID) REFERENCES USERS(userID) ON DELETE CASCADE,
    CHECK (receiverID <> senderID)
);


CREATE TABLE GROUPS (
    groupID SERIAL PRIMARY KEY,
    groupName VARCHAR(30) NOT NULL,
    description TEXT,
    visibilityPublic BOOLEAN NOT NULL,
    ownerID INTEGER UNIQUE NOT NULL,
    FOREIGN KEY (ownerID) REFERENCES USERS(userID) ON DELETE CASCADE
);

--Add column with the pre computed ts_vectors
ALTER TABLE Groups ADD COLUMN search TSVECTOR;

CREATE TABLE GROUP_MEMBERSHIP (
    groupID INTEGER NOT NULL,
    userID INTEGER NOT NULL,
    PRIMARY KEY (groupID, userID),
    FOREIGN KEY (groupID) REFERENCES GROUPS(groupID) ON DELETE CASCADE,
    FOREIGN KEY (userID) REFERENCES USERS(userID) ON DELETE CASCADE
);


CREATE TABLE JOIN_GROUP_REQUEST (
    groupID INTEGER NOT NULL,
    userID INTEGER NOT NULL,
    date TIMESTAMP NOT NULL,
    state TEXT NOT NULL CHECK (state IN ('Pending', 'Accepted', 'Rejected')),
    PRIMARY KEY (groupID, userID),
    FOREIGN KEY (groupID) REFERENCES GROUPS(groupID) ON DELETE CASCADE,
    FOREIGN KEY (userID) REFERENCES USERS(userID) ON DELETE CASCADE
);


CREATE TABLE POST (
    postID SERIAL PRIMARY KEY,
    userID INTEGER NOT NULL,
    message TEXT NOT NULL,
    visibilityPublic BOOLEAN NOT NULL,
    createdDate TIMESTAMP NOT NULL,
    groupID INTEGER,
    FOREIGN KEY (userID) REFERENCES USERS(userID) ON DELETE CASCADE,
    FOREIGN KEY (groupID) REFERENCES GROUPS(groupID) ON DELETE SET NULL
);

--Add column with the pre computed ts_vectors
ALTER TABLE Post ADD COLUMN search TSVECTOR;

CREATE TABLE TOPIC (
    topicID SERIAL PRIMARY KEY,
    topicName VARCHAR(30) NOT NULL
);

--Add column with the pre computed ts_vectors
ALTER TABLE Topic ADD COLUMN search TSVECTOR;

CREATE TABLE COMMENT (
    commentID SERIAL PRIMARY KEY,
    userID INTEGER NOT NULL,
    message TEXT NOT NULL,
    createdDate TIMESTAMP NOT NULL,
    postID INTEGER,
    parentCommentID INTEGER,  -- Parent comment if exists
    CHECK ((postID IS NOT NULL AND parentCommentID IS NULL) OR (postID IS NULL AND parentCommentID IS NOT NULL)),
    FOREIGN KEY (userID) REFERENCES USERS(userID) ON DELETE CASCADE,
    FOREIGN KEY (postID) REFERENCES POST(postID) ON DELETE CASCADE,
    FOREIGN KEY (parentCommentID) REFERENCES COMMENT(commentID) ON DELETE CASCADE
);

--Add column with the pre computed ts_vectors
ALTER TABLE Comment ADD COLUMN search TSVECTOR;


CREATE TABLE LIKES (
    likeID SERIAL PRIMARY KEY,
    userID INTEGER NOT NULL,
    createdDate TIMESTAMP NOT NULL,
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
    CHECK ((postID IS NOT NULL AND commentID IS NULL and userID IS NULL) OR (postID IS NULL AND commentID IS NOT NULL and userID IS NULL) OR (postID IS NULL AND commentID IS NULL and userID IS NOT NULL)),
    FOREIGN KEY (postID) REFERENCES POST(postID) ON DELETE CASCADE,
    FOREIGN KEY (commentID) REFERENCES COMMENT(commentID) ON DELETE CASCADE,
    FOREIGN KEY (userID) REFERENCES USERS(userID) ON DELETE CASCADE

);

CREATE TABLE FOLLOW (
    followerID INTEGER NOT NULL,
    followeeID INTEGER NOT NULL,
    state TEXT NOT NULL CHECK (state IN ('Pending', 'Accepted', 'Rejected')),
    followDate TIMESTAMP NOT NULL,
    CHECK (followerID <> followeeID),
    PRIMARY KEY (followerID, followeeID),
    FOREIGN KEY (followerID) REFERENCES USERS(userID) ON DELETE CASCADE,
    FOREIGN KEY (followeeID) REFERENCES USERS(userID) ON DELETE CASCADE,
	CHECK (followerID <> followeeID)
);

CREATE TABLE NOTIFICATION (
    notificationID SERIAL PRIMARY KEY,
    receiverID INTEGER NOT NULL,
    date TIMESTAMP NOT NULL,
    seen BOOLEAN NOT NULL,
    followID INTEGER,
    commentID INTEGER,
    likeID INTEGER,
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
    postID INTEGER,
    commentID INTEGER,
    reason TEXT,
    CHECK ((postID IS NOT NULL AND commentID IS NULL) OR (postID IS NULL AND commentID IS NOT NULL)),
    FOREIGN KEY (userID) REFERENCES USERS(userID) ON DELETE CASCADE,
    FOREIGN KEY (postID) REFERENCES POST(postID) ON DELETE CASCADE,
    FOREIGN KEY (commentID) REFERENCES COMMENT(commentID) ON DELETE CASCADE
);


CREATE TABLE GROUP_INVITATION (
    groupID INTEGER NOT NULL,
    userID INTEGER NOT NULL,
    date TIMESTAMP NOT NULL,
    state TEXT NOT NULL CHECK (state IN ('Pending', 'Accepted', 'Rejected')),
    PRIMARY KEY (groupID, userID),
    FOREIGN KEY (groupID) REFERENCES GROUPS(groupID) ON DELETE CASCADE,
    FOREIGN KEY (userID) REFERENCES USERS(userID) ON DELETE CASCADE
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

CREATE INDEX receiver_idx ON notification USING hash (receiveriD);

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
IF NEW.groupName <> OLD.groupName OR NEW.description <> NEW.description THEN 
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
    NEW.search = to_tsvector('english', NEW.name); 
END IF; 
IF TG_OP = 'UPDATE' THEN 
IF NEW.name <> OLD.name THEN 
        NEW.search = to_tsvector('english', NEW.name); 
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



CREATE OR REPLACE FUNCTION notify_content_owner_on_like()
RETURNS TRIGGER AS $$
DECLARE
    content_owner INTEGER;
BEGIN
    -- Check if the like is for a post and get the post owner
    IF NEW.postID IS NOT NULL THEN
        SELECT userID INTO content_owner FROM POST WHERE postID = NEW.postID;

    -- If the like is for a comment, get the comment owner
    ELSIF NEW.commentID IS NOT NULL THEN
        SELECT userID INTO content_owner FROM COMMENT WHERE commentID = NEW.commentID;
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

    -- If the like is for a comment, get the comment owner
    ELSIF NEW.parentCommentID IS NOT NULL THEN
        SELECT userID INTO content_owner FROM COMMENT WHERE commentID = NEW.parentCommentID;
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
    VALUES (NEW.followeeID, NEW.createdDate, FALSE, NEW.followerID);

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
    IF NOT EXISTS (SELECT * FROM GROUP_MEMBERSHIP WHERE NEW.userID = GROUP_MEMBERSHIP.userID AND NEW.groupID = GROUP_MEMBERSHIP.groupID AND NEW.groupID IS NOT NULL)
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
    AND NOT EXISTS (SELECT 1 FROM GROUP_MEMBERSHIP WHERE NEW.postID = POST.postID AND POST.groupID = GROUP_MEMBERSHIP.groupID AND NEW.userID = GROUP_MEMBERSHIP.userID) 
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
    AND NOT EXISTS (SELECT 1 FROM GROUP_MEMBERSHIP WHERE NEW.postID = POST.postID AND POST.groupID = GROUP_MEMBERSHIP.groupID AND NEW.userID = GROUP_MEMBERSHIP.userID) 
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
    IF EXISTS (SELECT * FROM GROUP_MEMBERSHIP WHERE NEW.userID = userID AND groupID IS NOT NULL) THEN
    RAISE EXCEPTION 'A user cannot request to join a group that he is already member of';
    END IF;
    RETURN NEW;
END
$$ LANGUAGE 'plpgsql';

--Create trigger to apply the verify_group_join_request() function when the table is updated (TRIGGER 09)
CREATE TRIGGER verify_group_join_request
    BEFORE INSERT OR UPDATE ON JOIN_GROUP_REQUEST
    FOR EACH ROW
    EXECUTE PROCEDURE verify_group_join_request();


-- Create function to verify that a comment date is equal to or greater than the post creation date
CREATE OR REPLACE FUNCTION verify_comment_date() RETURNS TRIGGER AS $$
BEGIN 
    IF NOT EXISTS (SELECT * FROM POST WHERE NEW.createdDate >= POST.createdDate AND NEW.postID = POST.postID) THEN
        RAISE EXCEPTION 'A comment date must be equal to or greater than the post creation date';
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
    IF NOT EXISTS (SELECT * FROM POST WHERE NEW.createdDate >= POST.createdDate AND NEW.postID = POST.postID) THEN
        RAISE EXCEPTION 'A like date must be equal to or greater than the post creation date';
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
    IF NOT EXISTS (SELECT * FROM COMMENT WHERE NEW.createdDate >= COMMENT.createdDate AND NEW.commentID = COMMENT.commentID) THEN
        RAISE EXCEPTION 'A like date must be equal to or greater than the comment creation date';
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
CREATE OR REPLACE FUNCTION verify_reply_comment_date() RETURNS TRIGGER AS $$
BEGIN 
    IF NOT EXISTS (SELECT * FROM COMMENT WHERE NEW.createdDate >= COMMENT.createdDate AND NEW.parentCommentID = COMMENT.commentID) THEN
        RAISE EXCEPTION 'A reply comment date must be equal to or greater than the original comment creation date';
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

