<?php

/* ========================================================================
 * $Id: message.php 9400 2020-04-30 20:44:49Z onez $
 * 
 * Email: www@onez.cn
 * QQ: 6200103
 * HomePage: http://www.onezphp.com
 * ========================================================================
 * Copyright 2016-2017 佳蓝科技.
 * 
 * ======================================================================== */

!defined('IN_ONEZ') && exit('Access Denied');
$json['html']=<<<ONEZ
<div class="row" v-for="item in data.record">
	<!-- 动态事件 -->
	<template v-if="item.type=='goto'" >
		<div class="goto" :class="onez.msgGoto(item.goto,data.record)"></div>
	</template>
	<!-- 系统消息 -->
	<template v-else-if="item.type=='vue'" >
		<onez :type="item.vueType" :data="item.vueData"></onez>
	</template>
	<!-- 系统消息 -->
	<template v-else-if="item.type=='system'" >
		<div class="system" :data-msgid="item.msg.id" data-menus="message">
			<!-- 文字消息 -->
			<div v-if="item.msg.type=='text'" class="text">
				{{item.msg.content.text}}
			</div>
			<!-- 领取红包消息 -->
			<div v-if="item.msg.type=='redEnvelope'" class="red-envelope">
				<img :src="onez.images.redEnvelopeChat">
				{{item.msg.content.text}}
			</div>
		</div>
	</template>
	<template v-else-if="item.type=='user'" >
		<!-- 自己发出的消息 -->
		<div class="my" v-if="item.from=='my'" :data-msgid="item.msg.id" data-menus="message">
			<!-- 左-消息 -->
			<div class="left">
				<!-- 文字消息 -->
				<div v-if="item.msg.type=='text'" :style="onez.info.style_my_bubble" class="bubble">
					<div class="co" v-html="item.msg.content.text"></div>
				</div>
				<!-- 语言消息 -->
				<div v-if="item.msg.type=='voice'" :style="onez.info.style_my_bubble" class="bubble voice" @tap="playVoice(item.msg)" :class="playMsgid == item.msg.id?'play':''">
					<div class="length">{{item.msg.content.length}}</div>
          <img :src="onez.images.icon_my_voice" :style="onez.info.style_icon_my_voice" class="onez-icon" />
				</div>
				<!-- 图片消息 -->
				<div v-if="item.msg.type=='img'" class="bubble img">
					<img :src="item.msg.content.thumb||item.msg.content.url" :data-bigpic="item.msg.content.url" :style="{'width': item.msg.content.w+'px','height': item.msg.content.h+'px'}" />
				</div>
				<!-- 视频消息 -->
				<div v-if="item.msg.type=='video'" :style="onez.info.style_my_video" class="bubble video" @tap="emitEvent(item.msg)">
          <img :src="item.msg.content.poster" :style="{'width': item.msg.content.w+'px','height': item.msg.content.h+'px'}" />
          <tui-icon name="play" color="#fff" :size="24"></tui-icon>
				</div>
				<!-- 红包 -->
				<div v-if="item.msg.type=='redEnvelope'" class="bubble red-envelope" @tap="openRedEnvelope(item.msg,index)">
				  <img :src="onez.images.redEnvelope" />
					<div class="tis">
						<!-- 点击开红包 -->
					</div>
					<div class="blessing">
						{{item.msg.content.blessing}}
					</div>
				</div>
				
			</div>
			<!-- 右-头像 -->
			<div class="right">
				<img :src="item.msg.userinfo.avatar" />
			</div>
		</div>
		<!-- 别人发出的消息 -->
		<div class="other" v-if="item.from=='other'" :data-msgid="item.msg.id" data-menus="message">
			<!-- 左-头像 -->
			<div class="left">
				<img :src="item.msg.userinfo.avatar" />
			</div>
			<!-- 右-用户名称-时间-消息 -->
			<div class="right">
				<div class="username">
					<div class="name">{{item.msg.userinfo.nickname}}</div> <div class="time">{{onez.time_str(item.msg.time)}}</div>
				</div>
				<!-- 文字消息 -->
				<div v-if="item.msg.type=='text'" :style="onez.info.style_other_bubble" class="bubble">
					<div class="co" v-html="item.msg.content.text"></div>
				</div>
				<!-- 语音消息 -->
				<div v-if="item.msg.type=='voice'" :style="onez.info.style_other_bubble" class="bubble voice" @tap="playVoice(item.msg)" :class="playMsgid == item.msg.id?'play':''">
          <img :src="onez.images.icon_other_voice" :style="onez.info.style_icon_other_voice" class="onez-icon" />
					<div class="length">{{item.msg.content.length}}</div>
				</div>
				<!-- 图片消息 -->
				<div v-if="item.msg.type=='img'" class="bubble img">
					<img :src="item.msg.content.thumb||item.msg.content.url" :data-bigpic="item.msg.content.url" :style="{'width': item.msg.content.w+'px','height': item.msg.content.h+'px'}" />
				</div>
				<!-- 视频消息 -->
				<div v-if="item.msg.type=='video'" :style="onez.info.style_other_video" class="bubble video" @tap="emitEvent(item.msg)">
          <img :src="item.msg.content.poster" :style="{'width': item.msg.content.w+'px','height': item.msg.content.h+'px'}" />
          <tui-icon name="play" color="#fff" :size="24"></tui-icon>
				</div>
				<!-- 红包 -->
				<div v-if="item.msg.type=='redEnvelope'" class="bubble red-envelope" @tap="openRedEnvelope(item.msg,index)">
				  <img :src="onez.images.redEnvelope" />
					<div class="tis">
						<!-- 点击开红包 -->
					</div>
					<div class="blessing">
						{{item.msg.content.blessing}}
					</div>
				</div>
			</div>
		</div>
	</template>
</div>
ONEZ;
$json['js']=<<<ONEZ

ONEZ;
$json['less']=<<<ONEZ

	.comp-message{
		padding: 0 25px;
		.loading{
			//loading动画
			display: flex;
			justify-content: center;
			@keyframes stretchdelay {
				0%, 40%, 100% {
					transform: scaleY(0.6);
				}
				20% {
					transform: scaleY(1.0);
				}
			}
			.spinner {
				margin: 10px 0;
				width: 30px;
				height: 50px;
				display: flex;
				align-items: center;
				justify-content: space-between;
				div {
					background-color: #f06c7a;
					height: 25px;
					width: 3px;
					border-radius: 3px;
					animation: stretchdelay 1.2s infinite ease-in-out;
				}
				.rect2 {
				  animation-delay: -1.1s;
				}
				.rect3 {
				  animation-delay: -1.0s;
				}
				.rect4 {
				  animation-delay: -0.9s;
				}
				.rect5 {
				  animation-delay: -0.8s;
				}
			}
		}
		.row{
			.system{
				display: flex;
				justify-content: center;
				div{
					padding: 0 15px;
					height: 25px;
					display: flex;
					justify-content: center;
					align-items: center;
					background-color: #c9c9c9;
					color: #fff;
					font-size: 12px;
					border-radius: 20px;
				}
				.red-envelope{
					img{
						margin-right: 3px;
						width: 15px;
						height: 15px;
					}
				}
			}
			&:first-child{
				margin-top: 10px;
			}
			padding: 10px 0;
			.my .left,.other .right{
				width: 100%;
				display: flex;
				.bubble{
					max-width: 70%;
					min-height: 25px;
					border-radius: 5px;
					padding: 7px 10px;
					display: flex;
					align-items: center;
					font-size: 16px;
					word-break: break-word;
					&.img{
						background-color: transparent;
						padding:0;
						overflow: hidden;
						img{
							max-width: 175px;
							max-height: 175px;
						}
					}
					&.video{
						background-color: transparent;
						padding:0;
						overflow: hidden;
						img{
							max-width: 175px;
							max-height: 175px;
						}
					}
					&.red-envelope{
						background-color: transparent;
						padding:0;
						overflow: hidden;
						position: relative;
						justify-content: center;
						align-items: flex-start;
						img{
							width: 125px;
							height: 156px;
						}
						.tis{
							position: absolute;
							top: 6%;
							font-size: 13px;
							color: #9c1712;
						}
						.blessing{
							position: absolute;
							bottom: 14%;
							color: #e9b874;
							width: 80%;
							text-align: center;
							overflow: hidden;
							// 最多两行
							display: -webkit-box;-webkit-box-orient: vertical;-webkit-line-clamp: 2;
						}
					}
					&.voice{
						.onez-icon{
							font-size: 20px;
							display: flex;
							align-items: center;
						}
						.onez-icon:after
						{
							content:" ";
							width: 26px;
							height: 26px;
							border-radius: 100%;
							position: absolute;
							box-sizing: border-box;
						}
						.length{
							font-size: 14px;
						}
					}
				}
			}
			.my .right,.other .left{
				flex-shrink: 0;
				width: 40px;
				height: 40px;
				img{
					width: 40px;
					height: 40px;
					border-radius: 5px;
				}
			}
			.my{
				width: 100%;
				display: flex;
				justify-content: flex-end;
				.left{
					min-height: 40px;
					
					align-items: center;
					justify-content: flex-end;
					.bubble{
						background-color: #f06c7a;
						color: #fff;
						
						&.voice{
							.onez-icon{
								color: #fff;
							}
							.length{
								margin-right: 10px;
							}
						}
						&.play{
							@keyframes my-play {
								0% {
									transform: translateX(80%);
								}
								100% {
									transform: translateX(0%);
								}
							}
							.onez-icon:after
							{
								border-left: solid 5px rgba(240,108,122,.5);
								animation: my-play 1s linear infinite;
							}
						}
					}
				}
				.right{
					margin-left: 7px;
				}
			}
			.other{
				width: 100%;
				display: flex;
				.left{
					margin-right: 7px;
				}
				.right{
					flex-wrap: wrap;
					.username{
						width: 100%;
						height: 27px;
						font-size: 12px;
						color: #999;
						display: flex;
						.name{
							margin-right: 25px;
						}
					}
					.bubble{
						background-color: #fff;
						color: #333;
						&.voice{
							.onez-icon{
								color: #333;
								
							}
							.length{
								margin-left: 10px;
							}
						}
						&.play{
							@keyframes other-play {
								0% {
									transform: translateX(-80%);
								}
								100% {
									transform: translateX(0%);
								}
							}
							.onez-icon:after
							{
								border-right: solid 5px rgba(255,255,255,.8);
								
								animation: other-play 1s linear infinite;
							}
						}
					}
				}
			}
		}
	}
ONEZ;
