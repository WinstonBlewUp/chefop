import { useForm, usePage, router } from '@inertiajs/react';
import { useState } from 'react';

type Page = {
  id: number;
  title: string;
  slug: string;
  content: string;
  template: string;
  published: boolean;
};

type Props = {
  pages: Page[];
  templates: Record<string, string>;
};

type FormData = {
  title: string;
  slug: string;
  content: string;
  template: string;
  published: boolean;
};

export default function PagesIndex() {
  const { pages, templates } = usePage<Props>().props;
  const [editingPage, setEditingPage] = useState<Page | null>(null);

  const { data, setData, post, put, reset, processing, errors } = useForm<FormData>({
    title: '',
    slug: '',
    content: '',
    template: 'default',
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

    if (!data.title.trim()) {
      alert('Le titre est requis.');
      return;
    }

    const finalSlug = data.slug.trim() || slugify(data.title);

    const payload: FormData = {
      ...data,
      slug: finalSlug,
      published: type === 'publish',
    };

    if (editingPage) {
      router.put(`/dashboard/pages/${editingPage.id}`, payload, {
        headers: {
          'Content-Type': 'application/json',
        },
        preserveScroll: true,
        onSuccess: () => resetForm(),
      });
    } else {
      console.log('Payload envoyé :', payload);
      router.post('/dashboard/pages/', payload, {
        headers: {
          'Content-Type': 'application/json',
        },
        preserveScroll: true,
        onSuccess: () => resetForm(),
      })
    }
  };

  const handleEdit = (page: Page) => {
    setEditingPage(page);
    setData('title', page.title);
    setData('slug', page.slug);
    setData('content', page.content);
    setData('template', page.template);
    setData('published', !!page.published);
  };

  const handleDelete = (id: number) => {
    if (confirm('Supprimer cette page ?')) {
      router.delete(`/dashboard/pages/${id}`);
    }
  };

  const handleShow = (page: Page) => {
  if (page.published) {
    // Redirection vers la page publique (ex: /pages/slug)
    window.open(`/pages/${page.slug}`, '_blank'); // Ouvre dans un nouvel onglet
  } else {
    alert("La page n'est pas publiée.");
  }
  };


  const resetForm = () => {
    setEditingPage(null);
    reset();
    setData('template', 'default');
    setData('published', false);
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
          {errors.title && <p className="text-red-500 text-sm">{errors.title}</p>}
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

        <div>
          <label className="block font-medium">Template</label>
          <select
            className="w-full border px-3 py-2 rounded"
            value={data.template}
            onChange={(e) => {
              const selected = e.target.value;
              setData('template', selected);

              if (!editingPage) {
                switch (selected) {
                  case 'legal':
                    setData('content', 'Mentions légales à compléter...');
                    break;
                  case 'contact':
                    setData('content', 'Page contact avec formulaire...');
                    break;
                  default:
                    setData('content', '');
                }
              }
            }}
          >
            {Object.entries(templates).map(([key, label]) => (
              <option key={key} value={key}>
                {label}
              </option>
            ))}
          </select>
        </div>

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
            className="bg-gray-800 text-white px-4 py-2 rounded hover:bg-gray-900"
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
              <th className="px-4 py-2">Template</th>
              <th className="px-4 py-2">Publié</th>
              <th className="px-4 py-2">Actions</th>
            </tr>
          </thead>
          <tbody>
            {pages.map((page) => (
              <tr key={page.id} className="border-t">
                <td className="px-4 py-2">{page.title}</td>
                <td className="px-4 py-2">{page.slug}</td>
                <td className="px-4 py-2">{page.template}</td>
                <td className="px-4 py-2">{page.published ? 'Oui' : 'Non'}</td>
                <td className="px-4 py-2 space-x-16 flex justify-center" style={{ justifyContent: 'space-around'}}>                  
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
