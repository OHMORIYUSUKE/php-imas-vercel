-- アイドルマスター キャラクターデータ
-- 文字エンコーディング設定
SET client_encoding = 'UTF8';
-- データベースの選択
-- PostgreSQLではデータベースを使用するには接続時に指定するため、この行は不要です。

-- 既存テーブルの削除
DROP TABLE IF EXISTS imas_characters;

-- テーブル定義
CREATE TABLE IF NOT EXISTS imas_characters (
       id SERIAL PRIMARY KEY,                          -- 自動インクリメントID
       type VARCHAR(100) NOT NULL,                     -- キャラクターの所属
       ch_name VARCHAR(50) NOT NULL,                   -- キャラクターのフルネーム
       ch_name_ruby VARCHAR(100) NOT NULL,             -- キャラクターのフルネームのよみがな
       ch_family_name VARCHAR(20) NOT NULL,            -- キャラクターの苗字
       ch_family_name_ruby VARCHAR(50) NOT NULL,       -- キャラクターの苗字のよみがな
       ch_first_name VARCHAR(20) NOT NULL,             -- キャラクターの名前
       ch_first_name_ruby VARCHAR(50) NOT NULL,        -- キャラクターの名前のよみがな
       ch_birth_month SMALLINT NOT NULL,               -- キャラクターの誕生月
       ch_birth_day SMALLINT NOT NULL,                 -- キャラクターの誕生日
       ch_gender SMALLINT NOT NULL,                    -- キャラクターの性別 0:男 1:女
       is_idol BOOLEAN NOT NULL DEFAULT TRUE,          -- アイドルかどうか（FALSE: いいえ、TRUE: はい）
       ch_blood_type VARCHAR(10),                      -- 血液型
       ch_color VARCHAR(20),                           -- キャラクターのイメージカラー
       cv_name VARCHAR(50),                            -- 声優さんのフルネーム
       cv_name_ruby VARCHAR(100),                      -- 声優さんのフルネームのよみがな
       cv_family_name VARCHAR(20),                     -- 声優さんの苗字
       cv_family_name_ruby VARCHAR(50),                -- 声優さんの苗字のよみがな
       cv_first_name VARCHAR(20),                      -- 声優さんの名前
       cv_first_name_ruby VARCHAR(50),                 -- 声優さんの名前のよみがな
       cv_birth_month SMALLINT,                        -- 声優さんの誕生月
       cv_birth_day SMALLINT,                          -- 声優さんの誕生日
       cv_gender SMALLINT,                             -- 声優さんの性別 0:男 1:女
       cv_nickname VARCHAR(50)                        -- 声優さんの愛称
);
