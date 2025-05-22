import { useForm, usePage, router } from '@inertiajs/react';
import { useState } from 'react';

type Page = {
  id: number;
  title: string;
  slug: string;
  content: string;
  project?: Project;
  category?: Category;
  published: boolean;
};

type Props = {
  pages: Page[];
  projects: Project[];
  categories: Category[];
};

type Category = {
  id: number;
  name: string;
};

type Project = {
  id: number;
  title: string;
};

type FormData = {
  title: string;
  slug: string;
  content: string;
  project_id?: string;
  category_id?: string;
  published: boolean;
};

export default function PagesIndex() {
  const { pages, projects, categories = [] } = usePage<Props>().props;
  const [editingPage, setEditingPage] = useState<Page | null>(null);
  const [isCollectionMode, setIsCollectionMode] = useState(false);

  const { data, setData, post, put, reset, processing, errors } = useForm<FormData>({
    title: '',
    slug: '',
    content: '',
    project_id: '',
    category_id: '',
    published: false,
  });

  const slugify = (str: string) =>
    str
      .toLowerCase()
      .normalize('NFD')
      .replace(/[\u0300-\u036f]/g, '')
      .replace(/[^a-z0-9]+/g, '-')
      .replace(/(^-|-$)+/g, '');

  const handleSubmit = (
    e: React.FormEvent,
    type: 'save' | 'publish'
  ) => {
    e.preventDefault();

    const finalSlug = data.slug.trim() || slugify(data.title);

    const payload: FormData = {
      ...data,
      slug: finalSlug,
      published: type === 'publish',
    };

    // Assurer qu’on n’envoie qu’un seul des deux IDs
    if (isCollectionMode) {
      delete payload.project_id;
    } else {
      delete payload.category_id;
    }

    if (editingPage) {
      router.put(`/dashboard/pages/${editingPage.id}`, payload, {
        preserveScroll: true,
        onSuccess: () => resetForm(),
      });
    } else {
      console.log(payload);
      router.post('/dashboard/pages/', payload, {
        preserveScroll: true,
        onSuccess: () => resetForm(),
      });
    }
  };

  const handleEdit = (page: Page) => {
    setEditingPage(page);
    setData('title', page.title);
    setData('slug', page.slug);
    setData('content', page.content);
    setData('published', page.published);

    if (page.project) {
      setIsCollectionMode(false);
      setData('project_id', page.project.id.toString());
      setData('category_id', '');
    } else if (page.category) {
      setIsCollectionMode(true);
      setData('category_id', page.category.id.toString());
      setData('project_id', '');
    } else {
      setIsCollectionMode(false);
      setData('project_id', '');
      setData('category_id', '');
    }
  };

  const resetForm = () => {
    setEditingPage(null);
    reset();
    setIsCollectionMode(false);
  };

  const handleDelete = (id: number) => {
    if (confirm('Supprimer cette page ?')) {
      router.delete(`/dashboard/pages/${id}`);
    }
  };

  const handleShow = (page: Page) => {
    if (page.published) {
      window.open(`/pages/${page.slug}`, '_blank');
    } else {
      alert("La page n'est pas publiée.");
    }
  };

  return (
    <div className="p-6">
      <h1 className="text-2xl font-bold mb-4">Gestion des pages</h1>

      <form className="bg-white p-4 rounded shadow mb-6 space-y-4">
        <div>
          <label className="block font-medium">Titre *</label>
          <input
            type="text"
            className="w-full border px-3 py-2 rounded"
            value={data.title}
            onChange={(e) => setData('title', e.target.value)}
            required
          />
        </div>

        <div>
          <label className="block font-medium">Slug</label>
          <input
            type="text"
            className="w-full border px-3 py-2 rounded"
            value={data.slug}
            onChange={(e) => setData('slug', e.target.value)}
          />
        </div>

        <div className="flex items-center">
          <label className="block font-medium mr-4">Associer à une collection</label>
          <input
            type="checkbox"
            checked={isCollectionMode}
            onChange={(e) => {
              setIsCollectionMode(e.target.checked);
              if (e.target.checked) {
                setData('project_id', '');
              } else {
                setData('category_id', '');
              }
            }}
          />
        </div>

        {isCollectionMode ? (
          <div>
            <label className="block font-medium">Collection</label>
            <select
              className="w-full border px-3 py-2 rounded"
              value={data.category_id || ''}
              onChange={(e) => setData('category_id', e.target.value)}
            >
              <option value="">Sélectionner une collection</option>
              {categories.map((cat) => (
                <option key={cat.id} value={cat.id.toString()}>
                  {cat.name}
                </option>
              ))}
            </select>
          </div>
        ) : (
          <div>
            <label className="block font-medium">Projet</label>
            <select
              className="w-full border px-3 py-2 rounded"
              value={data.project_id || ''}
              onChange={(e) => setData('project_id', e.target.value)}
            >
              <option value="">Sélectionner un projet</option>
              {projects.map((project) => (
                <option key={project.id} value={project.id.toString()}>
                  {project.title}
                </option>
              ))}
            </select>
          </div>
        )}

        <div>
          <label className="block font-medium">Contenu</label>
          <textarea
            className="w-full border px-3 py-2 rounded"
            rows={6}
            value={data.content}
            onChange={(e) => setData('content', e.target.value)}
          />
        </div>

        <div className="flex gap-2 mt-4">
          <button
            type="button"
            onClick={(e) => handleSubmit(e as unknown as React.FormEvent, 'save')}
            className="bg-gray-800 text-white px-4 py-2 rounded hover:bg-gray-900"
            disabled={processing}
          >
            Enregistrer
          </button>
          <button
            type="button"
            onClick={(e) => handleSubmit(e as unknown as React.FormEvent, 'publish')}
            className="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700"
            disabled={processing}
          >
            Publier
          </button>
          {editingPage && (
            <button
              type="button"
              onClick={resetForm}
              className="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600"
            >
              Annuler
            </button>
          )}
        </div>
      </form>

      <div className="bg-white shadow rounded">
        <table className="w-full table-auto border-collapse">
          <thead>
            <tr className="bg-gray-100 text-left text-sm">
              <th className="px-4 py-2">Titre</th>
              <th className="px-4 py-2">Slug</th>
              <th className="px-4 py-2">Source</th>
              <th className="px-4 py-2">Publié</th>
              <th className="px-4 py-2">Actions</th>
            </tr>
          </thead>
          <tbody>
            {pages.map((page) => (
              <tr key={page.id} className="border-t">
                <td className="px-4 py-2">{page.title}</td>
                <td className="px-4 py-2">{page.slug}</td>
                <td className="px-4 py-2">
                  {page.project?.title ?? (page.category ? `Collection : ${page.category.name}` : '—')}
                </td>
                <td className="px-4 py-2">{page.published ? 'Oui' : 'Non'}</td>
                <td className="px-4 py-2 space-x-4 flex justify-center">
                  <button
                    onClick={() => handleEdit(page)}
                    className="text-blue-600 hover:underline text-sm"
                  >
                    Modifier
                  </button>
                  <button
                    onClick={() => handleDelete(page.id)}
                    className="text-red-600 hover:underline text-sm"
                  >
                    Supprimer
                  </button>
                  <button
                    onClick={() => handleShow(page)}
                    className="text-green-600 hover:underline text-sm"
                  >
                    Voir
                  </button>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </div>
  );
}
