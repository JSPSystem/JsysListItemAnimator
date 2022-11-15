# EC-CUBE4.2 一覧商品アニメーションプラグイン
EC-CUBE4.2の商品一覧に簡単なアニメーションが設定できるようになるプラグインです。  
ダウンロード無料となっております。

## 目次
- [設定項目](#設定項目)
- [設定例](#設定例)
- [バージョンガイダンス](#バージョンガイダンス)
- [プラグインのダウンロードについて](#プラグインのダウンロードについて)
- [ライセンス](#ライセンス)

## 設定項目
#### 設定
基本の設定を行います。
| 項目名 | 値 | 説明 |
| --- | --- | --- |
| アニメーション前の状態 | 表示/非表示 | 非表示にすると設定された時間で透明から不透明になります。 |
| 移動 | 無効/有効 | 有効にすると設定された距離を移動します。 |
| 移動距離（横） | 数値、マイナス可 | 横方向の移動距離を設定します。-10の場合、左に10px移動して元の位置に戻ります。 |
| 移動距離（縦）| 数値、マイナス可 | 縦方向の移動距離を設定します。-10の場合、上に10px移動して元の位置に戻ります。 |
| アニメーションの時間（秒） | 数値、小数第2位まで可 | アニメーションに使用する時間を設定します。 |
| 順番にアニメーション | 無効/有効 | 有効にすると左上から順にアニメーションを開始できます。 |
| 時間差（秒） | 数値、小数第2位まで可 | 一つ前の開始から自身の開始までにどの程度ずらすか設定します。 |
| スクロールでアニメーション開始 | 無効/有効 | 有効にするとスクロールによってアニメーションを開始できます。 |
| 開始する位置（%） | 数値 | スクロール時に開始する位置を設定します。50であれば、商品上部がブラウザの中央より上になるタイミングで開始されます。 |
| 開始位置を表示 | 非表示/表示 | 表示にするとアニメーション開始位置を表示します。scroller-startがstartを超えるとstartの位置のアニメーションが開始します。※ 調整後は非表示にしてください |

#### マウスオーバー
マウスオーバー時の設定を行います。
| 項目名 | 値 | 説明 |
| --- | --- | --- |
| サイズを変更する | 無効/有効 | 有効にするとマウスオーバー時に拡大/縮小ができるようになります。 |
| 変更後のサイズ（%） | 数値 | マウスオーバー時の大きさを設定します。100未満で縮小、100より大きいと拡大されます。 |
| 変更にかかる時間（秒） | 数値、小数第2位まで可 | 設定されたサイズに変更するまでの時間を設定します。 |

## 設定例
#### 最初は透明、スクロールすると下から上にスライドしながら表示させたい
| 項目名 | 値 |
| --- | --- |
| アニメーション前の状態 | 非表示 |
| 移動 | 有効 |
| 移動距離（横） | 0 |
| 移動距離（縦）| -20 |
| アニメーションの時間（秒） | 1.3 |
| 順番にアニメーション | 無効 |
| スクロールでアニメーション開始 | 有効 |
| 開始する位置（%） | 55 |

#### 最初は透明、画面表示で順番に表示、マウスを乗せると1.15倍にしたい
| 項目名 | 値 |
| --- | --- |
| アニメーション前の状態 | 非表示 |
| 移動 | 無効 |
| アニメーションの時間（秒） | 3 |
| 順番にアニメーション | 有効 |
| 時間差（秒） | 0.2 |
| スクロールでアニメーション開始 | 無効 |
| サイズを変更する | 有効 |
| 変更後のサイズ（%） | 115 |
| 変更にかかる時間（秒） | 1.3 |

## バージョンガイダンス
| バージョン | EC-CUBEバージョン |
| --- | --- |
| v1.0.0 | 4.2 |

## プラグインのダウンロードについて
EC-CUBEオーナーズストアからダウンロードすることができます
| EC-CUBEバージョン | URL |
| --- | --- |
| 4.2 | https://www.ec-cube.net/products/detail.php?product_id=2604 |

## ライセンス
EC-CUBE4.2一覧商品アニメーションプラグインはLGPLの下でリリースされています。  
詳しくは同梱のLICENSEファイルをご覧ください。
