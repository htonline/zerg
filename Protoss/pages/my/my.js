// pages/my/my.js
Page({

  /**
   * 页面的初始数据
   */
  data: {
    
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
  },
  onGotUserInfo: function (e) {
    console.log(e.detail.errMsg)
    console.log(e.detail.userInfo)
    console.log(e.detail.rawData)
  },
  slider1change: function(e) {
    console.log(`slider1发生change事件，携带值为`, e.detail.value)
  },
  slider2change: function(e) {
    console.log(`slider2发生change事件，携带值为`, e.detail.value)
  },
  slider3change: function(e) {
    console.log(`slider3发生change事件，携带值为`, e.detail.value)
  },
  slider4change: function(e) {
    console.log(`slider4发生change事件，携带值为`, e.detail.value)
  },
  slider5change: function(e) {
    console.log(`slider5发生change事件，携带值为`, e.detail.value)
  },
  
})
