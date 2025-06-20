# php-serialize

**php-serialize** 是一个功能强大的基于属性（attribute）的 PHP 序列化库（需要 **PHP ≥ 8.1**）。  
它允许你将对象映射为数组或 JSON，并且可以基于相同的属性 **自动生成 OpenAPI 文档**。

> 🚀 统一解决方案，支持 API 数据序列化和文档生成。

## ✨ 功能特色

- 🏷️ 属性别名映射
- 🔄 自动类型转换（例如 `DateTime ↔ string`）
- 🔁 支持深度对象嵌套
- ❌ 支持跳过/排除字段
- 🧩 递归 DTO（数据传输对象）序列化
- 🧬 **基于对象定义自动生成 OpenAPI schema**
- ⚙️ 与框架无关 — 兼容 Laravel、Symfony 等框架  