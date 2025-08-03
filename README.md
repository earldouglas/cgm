## Roadmap

```mermaid
gantt
    title Roadmap
    dateFormat  YYYY-MM-DD

    section Phase 0
    Deploy Nightscout                     :done,   t0-1, 2025-07-31, 1d

    section Phase 1
    Setup backend                         :done,   t1-1, 2025-08-01, 1d
    Setup frontend                        :done,   t1-2, 2025-08-03, 1d
    Setup gateway                         :done,   t1-3, 2025-08-03, 1d

    section Phase 2
    Migrate the Add Treatments endpoint   :active, t2-1, after t1-3, 7d
    Migrate the rest of the endpoints     :        t2-2, after t2-1, 28d

    section Phase 3
    Remove frontend access to database    :        t3-0, after t2-2, 7d
```

### ~Phase 0~ (done)

```mermaid
flowchart LR

    User@{ shape: circle }
    MongoDB@{ shape: cylinder }
    Frontend@{ label: "Frontend\n(Nightscout)" }

    User --> Frontend

    Frontend --> MongoDB
```

This is our starting point.  Plain old Nightscout.

### Phase 1

```mermaid
flowchart LR

    User@{ shape: circle }
    Gateway@{ shape: diamond }
    MongoDB@{ shape: cylinder }
    Frontend@{ label: "Frontend\n(Nightscout)" }
    Backend@{ label: "Backend\n(Scala)" }

    User --> Gateway

    Gateway -->|/api/v4| Backend
    Gateway -->|/| Frontend

    Frontend --> MongoDB
    Backend --> MongoDB
```

Here, we add:

* A backend service with a wholly new API at `/api/v4/...`
    * Uses the existing authz roles for access control
* A gateway that:
    * Routes `/api/v4/*` to the backend
    * Routes everything else to the frontend

### Phase 2 (in progress)

```mermaid
flowchart LR

    User@{ shape: circle }
    Gateway@{ shape: diamond }
    MongoDB@{ shape: cylinder }
    Frontend@{ label: "Frontend\n(Nightscout)" }
    Backend@{ label: "Backend\n(Scala)" }

    User --> Gateway

    Gateway -->|"/api/{foo|bar|baz}"| Backend
    Gateway -->|/| Frontend

    Frontend --> MongoDB
    Backend --> MongoDB
```

Here, we incrementally move endpoints over from the frontend to the
backend.

### Phase 3 (next)

```mermaid
flowchart LR

    User@{ shape: circle }
    Gateway@{ shape: diamond }
    MongoDB@{ shape: cylinder }
    Frontend@{ label: "Frontend\n(Nightscout)" }
    Backend@{ label: "Backend\n(Scala)" }

    User --> Gateway

    Gateway -->|/api| Backend
    Gateway -->|/| Frontend

    Backend --> MongoDB
```

Once all the API endpoints have been moved to the backend, we remove the
frontend's access to the database.
