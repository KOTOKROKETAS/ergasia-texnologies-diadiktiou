-- file: create_follows.sql
CREATE TABLE IF NOT EXISTS follows (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  follower_id INT NOT NULL,
  followed_id INT NOT NULL,
  created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_follower FOREIGN KEY (follower_id)
            REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT fk_followed FOREIGN KEY (followed_id)
            REFERENCES users(id) ON DELETE CASCADE,
  CONSTRAINT uc_pair UNIQUE (follower_id, followed_id)
);
