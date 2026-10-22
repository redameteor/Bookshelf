<?php

return [
    'accepted' => ':attributeを承認してください。',
    'accepted_if' => ':otherが:valueの場合、:attributeを承認してください。',
    'active_url' => ':attributeは、有効なURLではありません。',
    'after' => ':attributeには、:dateより後の日付を指定してください。',
    'email' => ':attributeは、有効なメールアドレス形式で指定してください。',
    'required' => ':attributeは必須項目です。',
    'unique' => '指定された:attributeは既に登録されています。',

    'attributes' => [
        // 認証系フィールドのマッピング
        'name'                  => 'お名前',
        'email'                 => 'メールアドレス',
        'password'              => 'パスワード',
        'password_confirmation' => 'パスワード（確認用）',
        'current_password'      => '現在のパスワード',
        'remember'              => 'ログイン状態を保存',
    ],

];