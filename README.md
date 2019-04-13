# Marker Animation

[![License: GPL v2+](https://img.shields.io/badge/License-GPL%20v2%2B-blue.svg)](http://www.gnu.org/licenses/gpl-2.0.html)
[![PHP: >=5.6](https://img.shields.io/badge/PHP-%3E%3D5.6-orange.svg)](http://php.net/)
[![WordPress: >=4.6](https://img.shields.io/badge/WordPress-%3E%3D4.6-brightgreen.svg)](https://wordpress.org/)

![バナー](https://raw.githubusercontent.com/technote-space/marker-animation/images/.github/images/banner-772x250.png)

蛍光ペンで塗るようなアニメーションを表示する機能を追加するプラグインです。

[WordPress公式ディレクトリ](https://ja.wordpress.org/plugins/marker-animation/)

## スクリーンショット
### 動作

![動作](https://raw.githubusercontent.com/technote-space/marker-animation/images/.github/images/screenshot-1.gif)

### デフォルト設定画面

![設定画面](https://raw.githubusercontent.com/technote-space/marker-animation/images/.github/images/201901030129.png)

### マーカー設定画面（設定管理）

![一覧画面](https://raw.githubusercontent.com/technote-space/marker-animation/images/.github/images/201902051635.png)

![設定画面](https://raw.githubusercontent.com/technote-space/marker-animation/images/.github/images/201902051631.png)

### 投稿編集画面
#### ブロックエディタ
  
![アニメーションON](https://raw.githubusercontent.com/technote-space/marker-animation/images/.github/images/screenshot-9.gif)

![アニメーションOFF](https://raw.githubusercontent.com/technote-space/marker-animation/images/.github/images/screenshot-10.gif)

#### クラシックエディタ

![アニメーションON](https://raw.githubusercontent.com/technote-space/marker-animation/images/.github/images/screenshot-3.gif)

![アニメーションOFF](https://raw.githubusercontent.com/technote-space/marker-animation/images/.github/images/screenshot-4.gif)

## 要件
- PHP 5.6 以上
- WordPress 4.6 以上

## 導入手順
1. 管理画面のプラグインから「新規追加」  
![手順1](https://raw.githubusercontent.com/technote-space/marker-animation/images/.github/images/201901030113.png)  
2. 「蛍光ペン」で検索し「今すぐインストール」を押下  
![手順2](https://raw.githubusercontent.com/technote-space/marker-animation/images/.github/images/201901030114.png)
3. 「有効化」  

## 使用方法
1. 投稿画面のエディタでアニメーションを追加したい文章をマウスで選択
2. マーカーペンアイコンを押下
3. アニメーションを外したい場合は対象の文にカーソルを合わせた状態でマーカーペンアイコンを押下

## コントロールの種類
### ブロックエディタ
#### マーカーアニメーションボタン
デフォルト設定画面で設定した値でマーカーが動作します。
![ボタン1](https://raw.githubusercontent.com/technote-space/marker-animation/images/.github/images/201902051620.png)
#### マーカー設定で「ブロックエディタにボタン表示させるかどうか」を有効にして登録したボタン
マーカー設定で登録した値（空にした値はデフォルトの値）でマーカーが動作します。
![ボタン2](https://raw.githubusercontent.com/technote-space/marker-animation/images/.github/images/201902051621.png)
### クラシックエディタ
#### マーカーアニメーションボタン
デフォルト設定画面で設定した値でマーカーが動作します。
![ボタン1](https://raw.githubusercontent.com/technote-space/marker-animation/images/.github/images/201901030454.png)
#### マーカーアニメーション(詳細設定)ボタン
細かく値を指定することができます。
![ボタン2](https://raw.githubusercontent.com/technote-space/marker-animation/images/.github/images/201901030455.png)
#### マーカー設定で「クラシックエディタにボタン表示させるかどうか」を有効にして登録したボタン
マーカー設定で登録した値（空にした値はデフォルトの値）でマーカーが動作します。
![ボタン3](https://raw.githubusercontent.com/technote-space/marker-animation/images/.github/images/201901030134.png)
#### マーカー設定で「クラシックエディタにスタイルメニューに表示させるかどうか」を有効にして登録したメニュー
マーカー設定で登録した値（空にした値はデフォルトの値）でマーカーが動作します。  
![メニュー](https://raw.githubusercontent.com/technote-space/marker-animation/images/.github/images/201901030136.png)

## 設定
### 有効かどうか
マーカーアニメーションが有効かどうかを設定します。  
これを外すと全てのアニメーションが動作しなくなります。

### マーカーの色
マーカーの色を設定します。

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

## アニメーションなしでの利用
塗る時間と遅れ時間を0に設定するとアニメーションなしでの利用が可能です。

## Author
[GitHub (Technote)](https://github.com/technote-space)  
[Blog](https://technote.space)

## プラグイン作成用フレームワーク
[WP Content Framework](https://github.com/wp-content-framework/core)
