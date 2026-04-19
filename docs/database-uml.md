# Diagramme UML de la base de donnees

Ce schema se concentre sur les tables fonctionnelles du forum `Sphere`.
Les tables techniques Laravel (`cache`, `jobs`, `sessions`, `password_reset_tokens`) ne sont pas detaillees ici pour garder un diagramme lisible.

## Diagramme UML

```mermaid
classDiagram
direction LR

class User {
  +id : bigint
  +name : string
  +email : string
  +password : string
  +role : string
  +warning_count : int
  +is_blocked : bool
  +is_banned : bool
  +banned_until : timestamp?
  +level : int
  +experience : int
  +reputation : int
}

class Category {
  +id : bigint
  +name : string
  +slug : string
  +description : text?
}

class Topic {
  +id : bigint
  +user_id : bigint
  +category_id : bigint?
  +news_article_id : bigint?
  +title : string
  +slug : string
  +content : text
  +is_draft : bool
  +is_locked : bool
  +is_pinned : bool
}

class Reply {
  +id : bigint
  +topic_id : bigint
  +user_id : bigint
  +content : text
}

class Message {
  +id : bigint
  +sender_id : bigint
  +receiver_id : bigint
  +content : text
  +is_read : bool
}

class Notification {
  +id : uuid
  +type : string
  +notifiable_type : string
  +notifiable_id : bigint
  +data : text
  +read_at : timestamp?
}

class Tag {
  +id : bigint
  +name : string
  +slug : string
}

class TagTopic {
  +id : bigint
  +tag_id : bigint
  +topic_id : bigint
}

class TagUser {
  +id : bigint
  +tag_id : bigint
  +user_id : bigint
}

class Favorite {
  +id : bigint
  +user_id : bigint
  +topic_id : bigint
}

class ReplyLike {
  +id : bigint
  +reply_id : bigint
  +user_id : bigint
}

class ReplyBookmark {
  +id : bigint
  +reply_id : bigint
  +user_id : bigint
}

class Report {
  +id : bigint
  +user_id : bigint
  +topic_id : bigint?
  +reply_id : bigint?
  +reason : text?
  +status : string
}

class TopicEdit {
  +id : bigint
  +topic_id : bigint
  +old_content : text
}

class ReplyEdit {
  +id : bigint
  +reply_id : bigint
  +old_content : text
}

class Badge {
  +id : bigint
  +name : string
  +description : text?
}

class BadgeUser {
  +id : bigint
  +badge_id : bigint
  +user_id : bigint
}

class UserActivity {
  +id : bigint
  +user_id : bigint
  +type : string
  +description : text?
}

class AdminLog {
  +id : bigint
  +admin_id : bigint
  +action : string
  +details : text?
}

class NewsArticle {
  +id : bigint
  +category_id : bigint?
  +title : string
  +excerpt : text?
  +content : longText?
  +source_name : string?
  +source_url : string
  +image_url : string?
  +published_at : timestamp?
  +metadata : json?
}

class Announcement {
  +id : bigint
  +title : string
  +content : text
  +is_active : bool
}

class UserFollow {
  +id : bigint
  +follower_id : bigint
  +followed_id : bigint
}

class FollowRequest {
  +id : bigint
  +requester_id : bigint
  +requested_id : bigint
}

User "1" --> "0..*" Topic : cree
User "1" --> "0..*" Reply : ecrit
User "1" --> "0..*" Message : envoie
User "1" --> "0..*" Message : recoit
User "1" --> "0..*" Notification : recoit
User "1" --> "0..*" Favorite : ajoute
User "1" --> "0..*" ReplyLike : aime
User "1" --> "0..*" ReplyBookmark : sauvegarde
User "1" --> "0..*" Report : signale
User "1" --> "0..*" UserActivity : genere
User "1" --> "0..*" AdminLog : administre

Category "1" --> "0..*" Topic : classe
Category "1" --> "0..*" NewsArticle : regroupe

Topic "1" --> "0..*" Reply : contient
Topic "1" --> "0..*" Favorite : est_favori
Topic "1" --> "0..*" Report : est_signale
Topic "1" --> "0..*" TopicEdit : conserve
Topic "1" --> "0..*" TagTopic : associe

Reply "1" --> "0..*" ReplyLike : recoit
Reply "1" --> "0..*" ReplyBookmark : est_sauvegardee
Reply "1" --> "0..*" Report : est_signalee
Reply "1" --> "0..*" ReplyEdit : conserve

Tag "1" --> "0..*" TagTopic : classe
Tag "1" --> "0..*" TagUser : suit

Badge "1" --> "0..*" BadgeUser : attribue

NewsArticle "1" --> "0..*" Topic : inspire

User "1" --> "0..*" UserFollow : suit
User "1" --> "0..*" FollowRequest : demande
```

## Lecture rapide

- `users` est la table centrale.
- `topics` represente les publications principales du forum.
- `replies` represente les reponses a un sujet.
- `messages` gere la messagerie privee entre utilisateurs.
- `notifications` gere les notifications Laravel.
- `news_articles` permet de lier une actualite a un sujet de reaction.
- les tables pivot (`tag_topic`, `tag_user`, `badge_user`, `user_follows`, `follow_requests`) gerent les relations plusieurs-a-plusieurs ou auto-relations.

## Tables pivot importantes

- `tag_topic` : association entre sujets et tags
- `tag_user` : tags suivis par un utilisateur
- `badge_user` : badges attribues aux utilisateurs
- `user_follows` : relation utilisateur -> utilisateur
- `follow_requests` : demande d'abonnement avant validation
- `reply_bookmarks` : reponses enregistrees

