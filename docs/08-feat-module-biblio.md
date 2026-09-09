# 8 — Module Biblio : catalogue Livewire

Playbook pour le **CRUD catalogue** (livres, auteurs, catégories).

**Branche :** `feat/module-biblio`

---

## Objectif

Permettre à un ADMIN / BIBLIOTHECAIRE de gérer le catalogue : listes paginées, recherche, création / édition / détail / suppression (soft delete), avec permissions Spatie.

---

## Prérequis

- Auth + rôles Spatie (docs 01 → 03)
- Module nwidart `Modules/Biblio` créé (`php artisan module:make Biblio` si besoin)
- Layout `layouts.app` (sidebar + header)

---

## Structure

```
Modules/Biblio/
  app/
    Models/          Book, Author, Category
    Livewire/        *List, *Form, *Details
    Http/Controllers BookController, AuthorController, CategoryController
    Providers/       BiblioServiceProvider
  database/migrations/
  resources/views/
    books|authors|categories/index.blade.php
    livewire/*.blade.php
  routes/web.php
```

---

## Tables

| Table | Colonnes clés |
|-------|----------------|
| `categories` | `nom` unique, `description`, SoftDeletes |
| `authors` | `nom`, `prenom`, `biographie`, SoftDeletes |
| `books` | `category_id`, `titre`, `isbn`, `annee_parution`, `stock_total`, `stock_disponible`, `description`, SoftDeletes |
| `author_book` | pivot `author_id`, `book_id` |

Migrations : `Modules/Biblio/database/migrations/2026_08_20_*`.

---

## Relations Eloquent

| Modèle | Relation |
|--------|----------|
| `Book` | `category()` BelongsTo ; `authors()` BelongsToMany ; `emprunts()` HasMany |
| `Author` | `books()` BelongsToMany |
| `Category` | `books()` HasMany |

---

## Routes

Fichier : `Modules/Biblio/routes/web.php`  
Préfixe `biblio`, middleware `auth` + `verified`.

| URL | Nom | Permission |
|-----|-----|------------|
| `/biblio/books` | `biblio-book.list` | `voir-livres` |
| `/biblio/authors` | `biblio-author.list` | `voir-auteurs` |
| `/biblio/categories` | `biblio-category.list` | `voir-catégories` |

Les controllers ne font que `return view(...)`. Le CRUD vit dans Livewire.

---

## Livewire

| Composant | Rôle |
|-----------|------|
| `BookList` / `AuthorList` / `CategoryList` | Recherche `wire:model.live`, pagination, modals |
| `BookForm` / `AuthorForm` / `CategoryForm` | Create / update |
| `BookDetails` / `AuthorDetails` / `CategoryDetails` | Lecture |

Permissions UI : `@can('gérer-livres')`, `gérer-auteurs`, `gérer-catégories` pour create/edit/delete.

---

## Navigation

Sidebar (`resources/views/layouts/partials/sidebar.blade.php`) — section Catalogue :

- `route('biblio-book.list')` derrière `@can('voir-livres')`
- `route('biblio-author.list')` derrière `@can('voir-auteurs')`
- `route('biblio-category.list')` derrière `@can('voir-catégories')`

---

## Patterns à réutiliser (Emprunt UI)

- Index Blade mince + `<livewire:biblio::…-list />`
- Modal create/edit/details dans la liste
- Composant partagé `alert-message` pour les flash de succès / erreur
- SoftDeletes partout

---

## Voir aussi

- [README racine](../README.md) — modèle de données
- [03 — Rôles Spatie](./03-feat-auth-roles-spatie.md)
- [09 — Membres Emprunt](./09-feat-module-emprunt-membres.md) (même style de list Livewire)
