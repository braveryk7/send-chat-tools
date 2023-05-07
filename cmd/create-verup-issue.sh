#!/bin/sh

title="アップデート対応"
label="documentation"
assignee="@me"

body="
- [ ] readme.txtの更新（バージョン情報、更新履歴）
- [ ] admin-bar-tools.phpの更新（バージョン情報）
- [ ] GitHub Releaseの作成
- [ ] 記事反映
- [ ] SVN trunkへの追加
- [ ] アップデート告知
"

read -p "Versions?(vx.x.x): " inputVersion

gh issue create --title "${inputVersion}${title}" --body "${body}" --label ${label} --assignee ${assignee}