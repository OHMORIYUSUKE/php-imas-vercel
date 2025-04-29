# php-imas-vercel

アイドルマスターのキャラクター情報を検索できる Web アプリケーションです。

## 機能

- キャラクター名での検索
- 声優名での検索
- 血液型での絞り込み
- グループ（765 プロ、シンデレラガールズなど）での絞り込み

## ローカル開発環境のセットアップ

### 前提条件

- PHP 8.0 以上
- Vercel アカウント（PostgreSQL を使用するため）

### セットアップ手順

1. リポジトリのクローン

```bash
git clone https://github.com/OHMORIYUSUKE/php-imas-vercel.git
cd php-imas-vercel
```

2. Vercel の PostgreSQL の設定

- Vercel のダッシュボードにアクセス
- プロジェクトの「Storage」タブを開く
- 「Connect to Database」をクリックして PostgreSQL を有効化
- 接続情報を取得

3. 環境変数の設定
   `.env`ファイルを作成し、Vercel の PostgreSQL の接続情報を設定します：

```
POSTGRES_HOST=your-postgres-host
POSTGRES_PORT=5432
POSTGRES_DATABASE=your-database-name
POSTGRES_USER=your-username
POSTGRES_PASSWORD=your-password
```

4. データベースのセットアップ

- Vercel のダッシュボードにアクセス
- 「Storage」タブを開く
- 「Open in Neon」をクリック
- サイドバーの「SQL Editor」をクリック
- 以下の SQL ファイルの内容を順番に実行
  - `sql/000_character_create.sql`
  - `sql/character_765.sql`
  - `sql/character_cinderella_cute.sql`
  - `sql/idol_image.sql`

5. ローカルサーバーの起動

```bash
php -S localhost:8000 -t api/
```

6. ブラウザで確認
   `http://localhost:8000` にアクセスして動作確認

## デプロイ

Vercel にデプロイする場合は、以下の手順で行います：

1. GitHub リポジトリにプッシュ
2. Vercel のダッシュボードにアクセス
3. 「New Project」をクリック
4. 「Import Git Repository」から対象のリポジトリを選択
5. 環境変数を設定（ローカル開発環境と同じ設定）
6. 「Deploy」をクリック

デプロイ後は、GitHub リポジトリにプッシュするたびに自動的にデプロイが行われます。

## ライセンス

MIT License
