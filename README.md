# Attendance-app (勤怠管理アプリ)

## 環境構築

Laravelのスケジューラを有効にすつためのcorn設定　　

```
crontab -e　　
```
開いたファイルに、以下の１行を追加してください:  

```
-   -   -   -   - cd /path/to/project && ./vendor/bin/sail artisan schedule:run >> /dev/null 2>&1
```  

※ `/path/to/project` は、このプロジェクトを clone したディレクトリの絶対パスに置き換えてください。

# 初回実行について

cron の設定を行った時点が 0:00 を過ぎている場合、  
当日の勤怠レコードは自動生成されません。

そのため、初回のみ以下のコマンドを実行して  
勤怠レコードを手動で作成してください。

```
./vendor/bin/sail artisan attendance:create-daily
```

翌日以降は cron により毎日 0:00 に自動実行されます。


###
