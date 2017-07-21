# promise

PHP实现的promise示例,相关原理见[自己动手实现promise](https://mp.weixin.qq.com/s?__biz=MzIxNzg5ODE0OA==&mid=2247483686&idx=1&sn=eadf554c4e2cd770b8854477fa28d4e8&chksm=97f38ceda08405fbc7de90379d594a80392048c37a24f97e2f4ece43f470b7f9799e641e9248#rd)

## 环境准备

* 目前PHP实现异步超时接口为swoole\_timer\_after，故 **运行测试用例** 需要安装[zan扩展](https://github.com/youzan/zan)或者[swoole扩展](https://github.com/swoole/swoole-src)。
* 同步resolve和reject不需要安装上述扩展。

## 测试方法:

* cd test
* phpunit