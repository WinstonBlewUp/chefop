import { useForm, usePage } from '@inertiajs/react';
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

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();

    if (editingPage) {
      put(`/dashboard/pages/${editingPage.id}`, {
        preserveScroll: true,
        onSuccess: () => resetForm(),
      });
    } else {
      post('/dashboard/pages', {
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
    setData('template', page.template);
    setData('published', !!page.published);
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

      <form onSubmit={handleSubmit} className="bg-white p-4 rounded shadow mb-6 space-y-4">
        <div>
          <label className="block font-medium">Titre</label>
          <input
            type="text"
            className="w-full border px-3 py-2 rounded"
            value={data.title}
            onChange={(e) => setData('title', e.target.value)}
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

        <div className="flex items-center gap-2">
          <input
            type="checkbox"
            id="published"
            checked={data.published}
            onChange={(e) => setData('published', !!e.target.checked)}
          />
          <label htmlFor="published">Publié</label>
        </div>

        <div className="flex gap-2 mt-4">
          <button
            type="submit"
            className="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
            disabled={processing}
          >
            {editingPage ? 'Mettre à jour' : 'Créer'}
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
              <th className="px-4 py-2"></th>
            </tr>
          </thead>
          <tbody>
            {pages.map((page) => (
              <tr key={page.id} className="border-t">
                <td className="px-4 py-2">{page.title}</td>
                <td className="px-4 py-2">{page.slug}</td>
                <td className="px-4 py-2">{page.template}</td>
                <td className="px-4 py-2">{page.published ? 'Oui' : 'Non'}</td>
                <td className="px-4 py-2">
                  <button
                    onClick={() => handleEdit(page)}
                    className="text-blue-600 hover:underline text-sm"
                  >
                    Modifier
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
