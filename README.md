# 🦊 FoxNovel

**FoxNovel** es una plataforma web moderna y ligera diseñada para la lectura en línea de novelas ligeras, web novels y traducciones en español[cite: 1]. El proyecto está optimizado para ofrecer una navegación fluida, un catálogo extenso y un sistema de gestión de contenido seguro.

🌐 **Sitio Web Oficial:** [https://foxnovel.ct.ws](https://foxnovel.ct.ws)

---

## 🚀 Características Principales

* **Catálogo Extendido:** Más de 243 capítulos disponibles de diversas obras literarias e isekais[cite: 1].
* **Backend Robusto y Seguro:** Conectado a una base de datos PostgreSQL mediante **Supabase**, implementando políticas de seguridad de nivel de fila (*Row Level Security - RLS*).
* **Sistema de Creadores (Próximamente/Activo):** Espacio estructurado para la administración y publicación segura de obras por parte de autores independientes.
* **Optimización SEO y Social:** Configuración completa de etiquetas Open Graph (`og:image`, `og:title`, etc.) para una visualización perfecta al compartir enlaces en Facebook, WhatsApp y otras redes sociales.
* **Rendimiento optimizado:** Peticiones HTTP adaptadas (uso de métodos `GET` optimizados) para evitar bloqueos por parte de los cortafuegos del servicio de hosting.

---

## 🛠️ Tecnologías Utilizadas

* **Frontend:** HTML5, CSS3, JavaScript (Diseño Responsive).
* **Backend / Base de Datos:** PHP, Supabase (PostgreSQL).
* **Hosting:** InfinityFree (`ct.ws`)[cite: 1].
* **Control de Versiones:** Git y GitHub[cite: 1].

---

## 📂 Estructura del Proyecto

```text
Novela/
├── src/                # Recursos estáticos (imágenes, iconos, etc.)
├── assets/             # Hojas de estilo y scripts globales
├── index.php           # Página principal y catálogo
├── Style.css           # Estilos principales de la plataforma
└── ...                 # Archivos de visualización de capítulos y backend
```

🔒 Seguridad y Configuración de Base de Datos
El sistema utiliza Supabase para el almacenamiento seguro de los datos de usuarios, obras y capítulos. Las credenciales de conexión se gestionan de manera privada en el entorno del servidor para proteger la integridad de la base de datos.

👥 Contribución y Normas del Sistema de Autores
Para mantener la calidad y el orden en FoxNovel, el sistema de creación de contenido cuenta con las siguientes directrices:

Permisos de Administrador: La creación de nuevas obras requiere aprobación previa de la administración para salvaguardar la estabilidad del sistema.

Contenido Original: Está prohibido subir contenido protegido por derechos de autor de terceros haciéndolo pasar como propio.

Formato Estándar: Las publicaciones deben cumplir con los criterios mínimos de formato y ortografía.

📄 Licencia
Este proyecto se encuentra bajo los términos de uso independientes de FoxNovel. Todos los derechos reservados sobre las adaptaciones y el desarrollo de la plataforma.
