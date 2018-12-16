# Marker Animation

[![License: GPL v2+](https://img.shields.io/badge/License-GPL%20v2%2B-blue.svg)](http://www.gnu.org/licenses/gpl-2.0.html)
[![PHP: >=5.6](https://img.shields.io/badge/PHP-%3E%3D5.6-orange.svg)](http://php.net/)
[![WordPress: >=3.9.3](https://img.shields.io/badge/WordPress-%3E%3D3.9.3-brightgreen.svg)](https://wordpress.org/)

![バナー](https://raw.githubusercontent.com/technote-space/marker-animation/images/assets/banner-772x250.png)

蛍光ペンで塗るようなアニメーションを表示する機能を追加するプラグインです。

[WordPress公式ディレクトリ](https://ja.wordpress.org/plugins/marker-animation/)

## スクリーンショット
- 動作

![動作](https://raw.githubusercontent.com/technote-space/marker-animation/master/screenshot-1.gif)

- 設定画面

![設定画面](https://raw.githubusercontent.com/technote-space/marker-animation/master/screenshot-2.png)

- 投稿編集画面

![アニメーションON](https://raw.githubusercontent.com/technote-space/marker-animation/master/screenshot-3.gif)

![アニメーションOFF](https://raw.githubusercontent.com/technote-space/marker-animation/master/screenshot-4.gif)

![プリセットカラー](https://raw.githubusercontent.com/technote-space/marker-animation/master/screenshot-5.gif)

## 要件
- PHP 5.6 以上
- WordPress 3.9.3 以上

## 導入手順
1. ZIPダウンロード  
2. wp-content/plugins に展開  
3. 管理画面から有効化  

## 使用方法
1. 投稿画面のエディタでアニメーションを追加したい文章をマウスで選択
2. マーカーペンアイコンを押下
3. アニメーションを外したい場合は対象の文にカーソルを合わせた状態でマーカーペンアイコンを押下
4. マーカーペンアイコンは２つあります。左はデフォルト設定用、右は詳細設定用です。

## 設定
### 有効かどうか
マーカーアニメーションが有効かどうかを設定します。  
これを外すと全てのアニメーションが動作しなくなります。

### マーカーの色
マーカーの色を設定します。

### プリセットカラー
投稿画面から使用可能な色を設定します。  
設定したプリセットカラーはスタイルボタンから選択できます。

### マーカーの太さ
マーカーの太さを設定します。

### 塗る時間
マーカーを塗り終えるまでにかかる時間を設定します。  
0以上の数値＋単位で指定します。  
使用可能な単位は「s」と「ms」でそれぞれ秒とミリ秒です。

### 遅れ時間
表示されてからどれだけ時間が経過してからアニメーションを開始するかを指定します。
0、正の数、負の数＋単位で指定します。  
使用可能な単位は「塗る時間」と同様です。  
数値に負の数を指定した場合の動作等は[こちら](https://developer.mozilla.org/ja/docs/Web/CSS/transition-delay)を確認してください。

### 塗り方
マーカーの塗り方を設定します。  
詳細は[こちら](https://developer.mozilla.org/ja/docs/Web/CSS/transition-timing-function)を確認してください。  
設定画面では「ease」「linear」「ease-in」「ease-out」「ease-in-out」が選択できます。  
「cubic-bezier」等を指定したい場合は、詳細設定画面から設定します。

### 太文字にするかどうか
マーカーの対象を太文字にするかどうかを設定します。

### 繰り返すかどうか
画面から外れた後に再び表示された場合に再度アニメーションを行うかどうかを設定します。

### マーカー位置の調整
マーカーの表示位置を調整する値を設定します。
