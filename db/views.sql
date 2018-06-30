-- follow_requests_view
CREATE OR REPLACE ALGORITHM = UNDEFINED
VIEW `follow_requests_view`
AS
SELECT fr.*,u.name AS user,u.image AS user_image,f.name follower,f.image follower_image FROM follow_requests fr 
LEFT JOIN users u ON u.id=fr.user_id
LEFT JOIN users f ON f.id=fr.follow_id;
