# Marker Animation

[![CI Status](https://github.com/technote-space/marker-animation/workflows/CI/badge.svg)](https://github.com/technote-space/marker-animation/actions)
[![codecov](https://codecov.io/gh/technote-space/marker-animation/branch/master/graph/badge.svg)](https://codecov.io/gh/technote-space/marker-animation)
[![CodeFactor](https://www.codefactor.io/repository/github/technote-space/marker-animation/badge)](https://www.codefactor.io/repository/github/technote-space/marker-animation)
[![License: GPL v2+](https://img.shields.io/badge/License-GPL%20v2%2B-blue.svg)](http://www.gnu.org/licenses/gpl-2.0.html)
[![PHP: >=5.6](https://img.shields.io/badge/PHP-%3E%3D5.6-orange.svg)](http://php.net/)
[![WordPress: >=5.4](https://img.shields.io/badge/WordPress-%3E%3D5.4-brightgreen.svg)](https://wordpress.org/)

![バナー](https://raw.githubusercontent.com/technote-space/marker-animation/images/.github/images/banner-772x250.png)

*Read this in other languages: [English](README.md), [日本語](README.ja.md).*

蛍光ペンで塗るようなアニメーションを表示する機能を追加するプラグインです。

[最新バージョン](https://github.com/technote-space/marker-animation/releases/latest/download/release.zip)

## Table of Contents

<!-- START doctoc generated TOC please keep comment here to allow auto update -->
<!-- DON'T EDIT THIS SECTION, INSTEAD RE-RUN doctoc TO UPDATE -->
<details>
<summary>Details</summary>

- [スクリーンショット](#%E3%82%B9%E3%82%AF%E3%83%AA%E3%83%BC%E3%83%B3%E3%82%B7%E3%83%A7%E3%83%83%E3%83%88)
  - [動作](#%E5%8B%95%E4%BD%9C)
  - [ダッシュボード](#%E3%83%80%E3%83%83%E3%82%B7%E3%83%A5%E3%83%9C%E3%83%BC%E3%83%89)
  - [マーカー設定画面（設定管理）](#%E3%83%9E%E3%83%BC%E3%82%AB%E3%83%BC%E8%A8%AD%E5%AE%9A%E7%94%BB%E9%9D%A2%E8%A8%AD%E5%AE%9A%E7%AE%A1%E7%90%86)
  - [投稿編集画面](#%E6%8A%95%E7%A8%BF%E7%B7%A8%E9%9B%86%E7%94%BB%E9%9D%A2)
- [要件](#%E8%A6%81%E4%BB%B6)
- [導入手順](#%E5%B0%8E%E5%85%A5%E6%89%8B%E9%A0%86)
- [使用方法](#%E4%BD%BF%E7%94%A8%E6%96%B9%E6%B3%95)
- [コントロールの種類](#%E3%82%B3%E3%83%B3%E3%83%88%E3%83%AD%E3%83%BC%E3%83%AB%E3%81%AE%E7%A8%AE%E9%A1%9E)
  - [マーカーアニメーションボタン](#%E3%83%9E%E3%83%BC%E3%82%AB%E3%83%BC%E3%82%A2%E3%83%8B%E3%83%A1%E3%83%BC%E3%82%B7%E3%83%A7%E3%83%B3%E3%83%9C%E3%82%BF%E3%83%B3)
  - [マーカー設定で「ブロックエディタにボタン表示させるかどうか」を有効にして登録したボタン](#%E3%83%9E%E3%83%BC%E3%82%AB%E3%83%BC%E8%A8%AD%E5%AE%9A%E3%81%A7%E3%83%96%E3%83%AD%E3%83%83%E3%82%AF%E3%82%A8%E3%83%87%E3%82%A3%E3%82%BF%E3%81%AB%E3%83%9C%E3%82%BF%E3%83%B3%E8%A1%A8%E7%A4%BA%E3%81%95%E3%81%9B%E3%82%8B%E3%81%8B%E3%81%A9%E3%81%86%E3%81%8B%E3%82%92%E6%9C%89%E5%8A%B9%E3%81%AB%E3%81%97%E3%81%A6%E7%99%BB%E9%8C%B2%E3%81%97%E3%81%9F%E3%83%9C%E3%82%BF%E3%83%B3)
- [設定](#%E8%A8%AD%E5%AE%9A)
  - [有効かどうか](#%E6%9C%89%E5%8A%B9%E3%81%8B%E3%81%A9%E3%81%86%E3%81%8B)
  - [マーカーの色](#%E3%83%9E%E3%83%BC%E3%82%AB%E3%83%BC%E3%81%AE%E8%89%B2)
  - [マーカーの太さ](#%E3%83%9E%E3%83%BC%E3%82%AB%E3%83%BC%E3%81%AE%E5%A4%AA%E3%81%95)
  - [塗る時間](#%E5%A1%97%E3%82%8B%E6%99%82%E9%96%93)
  - [遅れ時間](#%E9%81%85%E3%82%8C%E6%99%82%E9%96%93)
  - [塗り方](#%E5%A1%97%E3%82%8A%E6%96%B9)
  - [太文字にするかどうか](#%E5%A4%AA%E6%96%87%E5%AD%97%E3%81%AB%E3%81%99%E3%82%8B%E3%81%8B%E3%81%A9%E3%81%86%E3%81%8B)
  - [ストライプデザインかどうか](#%E3%82%B9%E3%83%88%E3%83%A9%E3%82%A4%E3%83%97%E3%83%87%E3%82%B6%E3%82%A4%E3%83%B3%E3%81%8B%E3%81%A9%E3%81%86%E3%81%8B)
  - [繰り返すかどうか](#%E7%B9%B0%E3%82%8A%E8%BF%94%E3%81%99%E3%81%8B%E3%81%A9%E3%81%86%E3%81%8B)
  - [マーカー位置の調整](#%E3%83%9E%E3%83%BC%E3%82%AB%E3%83%BC%E4%BD%8D%E7%BD%AE%E3%81%AE%E8%AA%BF%E6%95%B4)
- [アニメーションなしでの利用](#%E3%82%A2%E3%83%8B%E3%83%A1%E3%83%BC%E3%82%B7%E3%83%A7%E3%83%B3%E3%81%AA%E3%81%97%E3%81%A7%E3%81%AE%E5%88%A9%E7%94%A8)
- [Dependency](#dependency)
- [Author](#author)
- [プラグイン作成用フレームワーク](#%E3%83%97%E3%83%A9%E3%82%B0%E3%82%A4%E3%83%B3%E4%BD%9C%E6%88%90%E7%94%A8%E3%83%95%E3%83%AC%E3%83%BC%E3%83%A0%E3%83%AF%E3%83%BC%E3%82%AF)

</details>
<!-- END doctoc generated TOC please keep comment here to allow auto update -->

## スクリーンショット
### 動作

![動作](https://raw.githubusercontent.com/technote-space/marker-animation/images/.github/images/screenshot-1.gif)

### ダッシュボード

![ダッシュボード](https://raw.githubusercontent.com/technote-space/marker-animation/images/.github/images/201905201411.png)

### マーカー設定画面（設定管理）

![一覧画面](https://raw.githubusercontent.com/technote-space/marker-animation/images/.github/images/201905201412.png)

![設定画面](https://raw.githubusercontent.com/technote-space/marker-animation/images/.github/images/201905201414.png)

### 投稿編集画面
  
![アニメーションON](https://raw.githubusercontent.com/technote-space/marker-animation/images/.github/images/screenshot-9.gif)

![アニメーションOFF](https://raw.githubusercontent.com/technote-space/marker-animation/images/.github/images/screenshot-10.gif)

## 要件
- PHP 5.6 以上
- WordPress 5.4 以上

## 導入手順
1. 最新版をGitHubからダウンロード  
[release.zip](https://github.com/technote-space/marker-animation/releases/latest/download/release.zip)
2. 「プラグインのアップロード」からインストール
![install](https://raw.githubusercontent.com/technote-space/screenshots/master/misc/install-wp-plugin.png)
3. プラグインを有効化 

## 使用方法
1. 投稿画面のエディタでアニメーションを追加したい文章をマウスで選択
2. マーカーペンアイコンを押下
3. アニメーションを外したい場合は対象の文にカーソルを合わせた状態でマーカーペンアイコンを押下

## コントロールの種類
### マーカーアニメーションボタン
デフォルト設定画面で設定した値でマーカーが動作します。  
![ボタン1](https://raw.githubusercontent.com/technote-space/marker-animation/images/.github/images/201902051620.png)  

サイドバーで細かく値を指定できます。 (WordPress v5.2以上)  
![詳細設定](https://raw.githubusercontent.com/technote-space/marker-animation/images/.github/images/201905201416.png)  
### マーカー設定で「ブロックエディタにボタン表示させるかどうか」を有効にして登録したボタン
マーカー設定で登録した値（空にした値はデフォルトの値）でマーカーが動作します。
![ボタン2](https://raw.githubusercontent.com/technote-space/marker-animation/images/.github/images/201902051621.png)

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

### ストライプデザインかどうか
ストライプデザインにするかどうかを設定します。  

![ストライプデザイン](https://raw.githubusercontent.com/technote-space/jquery.marker-animation/images/stripe.png)  

これが設定されている場合、アニメーションなしの動作になります。

### 繰り返すかどうか
画面から外れた後に再び表示された場合に再度アニメーションを行うかどうかを設定します。

### マーカー位置の調整
マーカーの表示位置を調整する値を設定します。

## アニメーションなしでの利用
塗る時間と遅れ時間を0に設定するとアニメーションなしでの利用が可能です。

## Dependency
- [jQuery Marker Animation](https://github.com/technote-space/jquery.marker-animation)
- [Register Grouped Format Type](https://github.com/technote-space/register-grouped-format-type)

## Author
[GitHub (Technote)](https://github.com/technote-space)  
[Blog](https://technote.space)

## プラグイン作成用フレームワーク
[WP Content Framework](https://github.com/wp-content-framework/core)
