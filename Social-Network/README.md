# Social-Network-v2 #

### ENVIRONMENT RUNTIME ###

#### DB SERVER ####

> FORMAT : utf8mb4_general_ci 
>
> STORAGE ENGINE : InnoDB 
>
> SERVER : MariaDB 10.4.11

#### WEB SERVER ####

> APACHE : 2.4.43 (Unix) 
>
> OpenSSL : 1.1.1f 
>
> PHP : 7.4.4 
>
> MOD_PERL : 2.0.8-dev 
>
> PERL : v5.16.3 

### DATABASE ###

##### COMMENTS : #####

Nom | Type | Interclassement | Extra
------------- | ------------- | ------------- | -------------
id | int(10) |  | AUTO_INCREMENT
post_body | text(160) | utf8mb4_general_ci |
posted_by | VARCHAR(45) | utf8mb4_general_ci |
posted_to | VARCHAR(45) | utf8mb4_general_ci |
date_added | datetime |  |
post_id | int(10) |  |

##### FRIEND_REQUESTS : #####

Nom | Type | Interclassement | Extra
------------- | ------------- | ------------- | -------------
id | int(10) |  | AUTO_INCREMENT
user_from | VARCHAR(45) | utf8mb4_general_ci |
user_to | VARCHAR(45) | utf8mb4_general_ci |

##### LIKES : #####

Nom | Type | Interclassement | Extra
------------- | ------------- | ------------- | -------------
id | int(10) |  | AUTO_INCREMENT
user_from | VARCHAR(45) | utf8mb4_general_ci |
post_id | int(10) |  | 

##### MESSAGES : #####

Nom | Type | Interclassement | Extra
------------- | ------------- | ------------- | -------------
id | int(10) |  | AUTO_INCREMENT
user_from | VARCHAR(45) | utf8mb4_general_ci |
user_to | VARCHAR(45) | utf8mb4_general_ci |
message | text(160) | utf8mb4_general_ci |
datetime | datetime |  |
viewed | ENUM('no', 'yes') | utf8mb4_general_ci |
opened | ENUM('no', 'yes') | utf8mb4_general_ci |
deleted | ENUM('no', 'yes') | utf8mb4_general_ci |

##### POSTS : #####

Nom | Type | Interclassement | Extra
------------- | ------------- | ------------- | -------------
id | int(10) |  | AUTO_INCREMENT
post_body | text(160) | utf8mb4_general_ci |
posted_by | VARCHAR(45) | utf8mb4_general_ci |
posted_to | VARCHAR(45) | utf8mb4_general_ci |
date_added | datetime |  |
image | VARCHAR(500) | utf8mb4_general_ci |
likes | int(10) |  |
removed | ENUM('no', 'yes') |  |
user_closed | ENUM('no', 'yes') | utf8mb4_general_ci |

##### USERS : #####

Nom | Type | Interclassement | Extra
------------- | ------------- | ------------- | -------------
id | int(10) |  | AUTO_INCREMENT
first_name | VARCHAR(20) | utf8mb4_general_ci |
last_name | VARCHAR(20) | utf8mb4_general_ci |
email | VARCHAR(100) | utf8mb4_general_ci |
username | VARCHAR(45) | utf8mb4_general_ci |
password | VARCHAR(255) | utf8mb4_general_ci |
signup_date | date |  |
profile_pic | VARCHAR(255) | utf8mb4_general_ci |
num_posts | int(10) |  |
num_likes | int(10) |  |
friend_array | text | utf8mb4_general_ci |
user_closed | ('no', 'yes') | utf8mb4_general_ci |

---

### .htaccess ###

> Root
```
Options All -Indexes
RewriteEngine On

RewriteRule ^([a-zA-Z0-9_-]+)$ profile.php?profile_username=$1
RewriteRule ^([a-zA-Z0-9_-]+)/$ profile.php?profile_username=$1

RewriteBase /

RewriteRule ^index\$ – [L]
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule . /index.php [L]
```
#### Add in : #### 
> - /config/
> - /model/
> - /view/games/
> - /view/dashboard/
```
<FilesMatch "\.php$">
    Require all denied
</FilesMatch>
```
