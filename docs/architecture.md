# FinHub Clean Architecture Blueprint

## 1. Flujos principales
- **Auth**: registro, login, refresh token, logout opcional.
- **Usuario/Cuenta**: obtener perfil, cambiar contraseña.
- **Portafolio**: crear portafolio, listar portafolios, ver detalle.
- **Holdings**: agregar instrumento, modificar cantidad/costo, eliminar.
- **Instrumentos**: catálogo global, búsqueda, detalles básicos.
- **Precios/Datos de mercado**: registrar OHLCV, consultar histórico, último precio.
- **Observabilidad/Infra**: registro de llamadas externas, auditoría básica.

## 2. Contratos de aplicación (pseudocódigo)

### Auth
- `RegisterUserService::execute(RegisterUserRequestDTO) -> RegisterUserResultDTO`
- `LoginService::execute(LoginRequestDTO) -> LoginResultDTO`
- `RefreshTokenService::execute(RefreshTokenRequestDTO) -> LoginResultDTO`
- `LogoutService::execute(UserId, TokenId) -> void`

### Usuario/Cuenta
- `GetProfileService::execute(UserId) -> UserProfileView`
- `ChangePasswordService::execute(ChangePasswordRequestDTO) -> void`

### Portafolio
- `CreatePortfolioService::execute(CreatePortfolioRequestDTO) -> PortfolioView`
- `ListPortfoliosService::execute(UserId) -> PortfolioSummaryList`
- `GetPortfolioService::execute(UserId, PortfolioId) -> PortfolioView`

### Holdings
- `AddHoldingService::execute(AddHoldingRequestDTO) -> HoldingView`
- `UpdateHoldingService::execute(UpdateHoldingRequestDTO) -> HoldingView`
- `RemoveHoldingService::execute(UserId, PortfolioId, HoldingId) -> void`

### Instrumentos
- `SearchInstrumentsService::execute(SearchInstrumentRequestDTO) -> InstrumentList`
- `GetInstrumentService::execute(InstrumentId) -> InstrumentView`

### Precios / Datos de mercado
- `IngestPriceBarsService::execute(InstrumentId, PriceBarsBatchDTO) -> void`
- `GetPriceHistoryService::execute(InstrumentId, PriceHistoryRequestDTO) -> PriceHistoryView`
- `GetLastPriceService::execute(InstrumentId) -> LastPriceView`

### Infraestructura transversal
- `LogExternalCallService::execute(ExternalCallLogDTO) -> void`

## 3. Modelo de dominio (entidades y reglas)

### User
- Atributos: `id`, `email`, `passwordHash`, `createdAt`, `updatedAt`.
- Reglas: email único y válido; `passwordHash` sólo se crea vía `PasswordHasher`; puede tener uno o varios portafolios.

### Portfolio
- Atributos: `id`, `userId`, `name`, `createdAt`, `updatedAt`.
- Reglas: pertenece a un único usuario; nombre requerido; eliminación en cascada controlada para holdings.

### Holding
- Atributos: `id`, `portfolioId`, `instrumentId`, `quantity`, `averageCost`, `createdAt`, `updatedAt`.
- Reglas: referencia a un `Instrument` global; `quantity` ≥ 0; `averageCost` ≥ 0; pertenecen a un único portafolio.

### Instrument
- Atributos: `id`, `symbol`, `name`, `currency`, `exchange`, `type`, `createdAt`, `updatedAt`.
- Reglas: `symbol` globalmente único; `currency` ISO-4217; `type` (equity, crypto, etf, etc.).

### PriceBar
- Atributos: `id`, `instrumentId`, `date`, `open`, `high`, `low`, `close`, `volume`, `createdAt`.
- Reglas: `date` única por instrumento; precios y volumen no negativos.

### ExternalApiLog
- Atributos: `id`, `source`, `endpoint`, `status`, `requestPayload`, `responsePayload`, `startedAt`, `finishedAt`.
- Reglas: persistir para auditoría/observabilidad; no impacta en dominios principales.

## 4. Principios de arquitectura (aplicables al módulo)
- Controladores delgados: sólo parsean HTTP y llaman a un caso de uso.
- Capa de aplicación orquesta lógica y depende de interfaces (repositorios, token, hashers).
- Capa de dominio es pura: sin referencias a HTTP/DB/JWT.
- Infraestructura implementa interfaces sin acoplar controladores.
- DI centralizada: contenedor/factory para compartir PDO, repositorios y servicios.
- Esquema de datos versionado en `db/schema.sql`; el código no crea/alterar tablas en runtime.

## 5. Checklist de contrato de datos
- Si cambian entidades o repositorios → actualizar `db/schema.sql`.
- Deploy ejecuta explícitamente `db/schema.sql` (o migración derivada) antes de correr la app.
- Sin auto-migraciones en runtime.
