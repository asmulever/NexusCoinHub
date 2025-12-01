# Flujo básico para commitear y bajar el código localmente

Este proyecto usa Git. A continuación se resume cómo preparar un commit, subirlo al remoto y clonar el repositorio para usarlo en tu máquina local.

## Preparar y crear un commit
1. Revisa el estado de los cambios:
   ```bash
   git status -sb
   ```
2. Añade los archivos modificados al área de preparación:
   ```bash
   git add <ruta/al/archivo>...
   # o para todo lo modificado
   git add .
   ```
3. Crea el commit con un mensaje claro (en español o inglés):
   ```bash
   git commit -m "Describe brevemente el cambio"
   ```

## Subir los commits al remoto
1. Verifica que tengas configurado el remoto (por ejemplo `origin`):
   ```bash
   git remote -v
   ```
2. Envía tus commits al repositorio remoto:
   ```bash
   git push origin <nombre-de-la-rama>
   ```

## Descargar el código localmente (clonar)
1. Clona el repositorio en tu máquina:
   ```bash
   git clone <url-del-repositorio>
   ```
2. Entra al directorio del proyecto y cambia a la rama deseada si corresponde:
   ```bash
   cd <carpeta-del-repo>
   git checkout <nombre-de-la-rama>
   ```

## Sincronizar cambios recientes
Si ya tienes el repositorio clonado y quieres traer los últimos cambios de la rama remota:
```bash
git pull origin <nombre-de-la-rama>
```

## Consejos rápidos
- Realiza commits pequeños y enfocados para facilitar las revisiones.
- Asegúrate de que las pruebas relevantes pasen antes de hacer push.
- Evita incluir archivos generados o credenciales sensibles en los commits.
