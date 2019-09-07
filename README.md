# Marker Animation

[![Build Status](https://github.com/technote-space/marker-animation/workflows/Build/badge.svg)](https://github.com/technote-space/marker-animation/actions)
[![Build Status](https://travis-ci.com/technote-space/marker-animation.svg?branch=master)](https://travis-ci.com/technote-space/marker-animation)
[![Coverage Status](https://coveralls.io/repos/github/technote-space/marker-animation/badge.svg?branch=master)](https://coveralls.io/github/technote-space/marker-animation?branch=master)
[![CodeFactor](https://www.codefactor.io/repository/github/technote-space/marker-animation/badge)](https://www.codefactor.io/repository/github/technote-space/marker-animation)
[![License: GPL v2+](https://img.shields.io/badge/License-GPL%20v2%2B-blue.svg)](http://www.gnu.org/licenses/gpl-2.0.html)
[![PHP: >=5.6](https://img.shields.io/badge/PHP-%3E%3D5.6-orange.svg)](http://php.net/)
[![WordPress: >=4.6](https://img.shields.io/badge/WordPress-%3E%3D4.6-brightgreen.svg)](https://wordpress.org/)

![Banner](https://raw.githubusercontent.com/technote-space/marker-animation/images/.github/images/banner-772x250.png)

*Read this in other languages: [English](README.md), [日本語](README.ja.md).*

This plugin will add the ability to display animations like painting with a highlighter.

[Demonstration](https://technote-space.github.io/marker-animation)

[Latest version](https://github.com/technote-space/marker-animation/releases/latest/download/marker-animation.zip)

<!-- START doctoc -->
<!-- END doctoc -->

## Screenshots
### Behavior

![Behavior](https://raw.githubusercontent.com/technote-space/marker-animation/images/.github/images/screenshot-1.gif)

### Dashboard

![Dashboard](https://raw.githubusercontent.com/technote-space/marker-animation/images/.github/images/201905201411.png)

### Marker setting

![Setting list](https://raw.githubusercontent.com/technote-space/marker-animation/images/.github/images/201905201412.png)

![Setting page](https://raw.githubusercontent.com/technote-space/marker-animation/images/.github/images/201905201414.png)

### Editor page
  
![On animation](https://raw.githubusercontent.com/technote-space/marker-animation/images/.github/images/screenshot-9.gif)

![Off animation](https://raw.githubusercontent.com/technote-space/marker-animation/images/.github/images/screenshot-10.gif)

## Requirements
- \>= PHP 5.6
- \>= WordPress 4.6

## Installation
1. Download latest version  
[marker-animation.zip](https://github.com/technote-space/marker-animation/releases/latest/download/marker-animation.zip)
1. Install plugin
![install](https://raw.githubusercontent.com/technote-space/screenshots/master/misc/install-wp-plugin.png)
1. Activate plugin

## Usage
1. Select sentence which you want to add animation.
1. Press apply animation button.
1. If you want to remove the animation, press the button with the cursor on the target sentence.

## Control types
### Default button
![Default button](https://raw.githubusercontent.com/technote-space/marker-animation/images/.github/images/201902051620.png)  

Details can be specified in the sidebar(\>=WordPress v5.2)

![Detail](https://raw.githubusercontent.com/technote-space/marker-animation/images/.github/images/201905201416.png)  

### Registered buttons
The buttons which is registered from `All Settings` will be gathered by Dropdown.
![Registered buttons](https://raw.githubusercontent.com/technote-space/marker-animation/images/.github/images/201902051621.png)

## Settings
### Validity
When this setting is off, all functions are disabled.

### Color
Specify the highlighter color.

### Thickness
Specify the highlighter thickness.

### Duration
Specify the time to finish animation.  
ex. `1.2s`  `.5s`  `800ms`

### Delay
Specify the time to start animation.  
ex. `1.2s`  `.5s`  `800ms`  
[Detail](https://developer.mozilla.org/ja/docs/Web/CSS/transition-delay)

### Function
Specify the transition timing function.  
[Detail](https://developer.mozilla.org/ja/docs/Web/CSS/transition-timing-function)  

### Font bold
Specify whether to display bold. 

### Stripe
Specify whether to display stripe design.

![Stripe](https://raw.githubusercontent.com/technote-space/jquery.marker-animation/images/stripe.png)  

### Repeat
Specify whether to repeat animation.

### Padding bottom
Specify padding bottom.

## Dependency
- [jQuery Marker Animation](https://github.com/technote-space/jquery.marker-animation)
- [Register Grouped Format Type](https://github.com/technote-space/register-grouped-format-type)

## Author
[GitHub (Technote)](https://github.com/technote-space)  
[Blog](https://technote.space)

## Plugin framework
[WP Content Framework](https://github.com/wp-content-framework/core)
