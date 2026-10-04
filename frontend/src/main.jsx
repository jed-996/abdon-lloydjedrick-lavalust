import React, { useEffect, useMemo, useState } from 'react';
import { createRoot } from 'react-dom/client';
import './styles.css';

const API = '/api';
const emptyProduct = { product_name: '', description: '', price: '', quantity: '0' };

function Icon({ name, size = 18 }) {
  const paths = {
    box: <><path d="m21 8-9-5-9 5 9 5 9-5Z"/><path d="m3 8 9 5 9-5M3 8v8l9 5 9-5V8M12 13v8"/></>,
    plus: <path d="M12 5v14M5 12h14"/>,
    search: <><circle cx="11" cy="11" r="7"/><path d="m20 20-4-4"/></>,
    edit: <><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L8 18l-4 1 1-4Z"/></>,
    trash: <><path d="M3 6h18M8 6V4h8v2M19 6l-1 14H6L5 6M10 11v5M14 11v5"/></>,
    logout: <><path d="M10 17l5-5-5-5M15 12H3M15 3h5a1 1 0 0 1 1 1v16a1 1 0 0 1-1 1h-5"/></>,
    close: <path d="m6 6 12 12M18 6 6 18"/>,
    alert: <><path d="M12 3 2.5 20h19Z"/><path d="M12 9v4M12 17h.01"/></>,
    refresh: <><path d="M20 11a8 8 0 1 0-2.3 5.7"/><path d="M20 4v7h-7"/></>,
  };
  return <svg className="icon" width={size} height={size} viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.8" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">{paths[name]}</svg>;
}

async function request(path, options = {}) {
  const token = sessionStorage.getItem('stockroom_access');
  const response = await fetch(`${API}${path}`, {
    ...options,
    headers: {
      'Content-Type': 'application/json',
      ...(token ? { Authorization: `Bearer ${token}` } : {}),
      ...(options.headers || {}),
    },
  });
  const data = await response.json().catch(() => ({}));
  if (!response.ok) {
    const error = new Error(data.error || 'The request could not be completed.');
    error.status = response.status;
    error.fields = data.errors || {};
    throw error;
  }
  return data;
}

function Login({ onLogin }) {
  const [form, setForm] = useState({ email: '', password: '' });
  const [error, setError] = useState('');
  const [busy, setBusy] = useState(false);

  async function submit(event) {
    event.preventDefault();
    setError('');
    setBusy(true);
    try {
      const data = await request('/auth/login', { method: 'POST', body: JSON.stringify(form) });
      sessionStorage.setItem('stockroom_access', data.tokens.access_token);
      sessionStorage.setItem('stockroom_refresh', data.tokens.refresh_token);
      sessionStorage.setItem('stockroom_user', JSON.stringify(data.user));
      onLogin(data.user);
    } catch (err) {
      setError(err.message);
    } finally {
      setBusy(false);
    }
  }

  return <main className="login-shell">
    <section className="login-panel" aria-labelledby="login-title">
      <div className="brand brand-large"><span>PRODUCT</span><b>STOCKROOM</b></div>
      <div className="login-copy">
        <span className="eyebrow">Laboratory Exercise No. 6</span>
        <h1 id="login-title">Welcome back.</h1>
        <p>Sign in to manage inventory through the authenticated LavaLust API.</p>
      </div>
      {error && <div className="alert" role="alert"><Icon name="alert" />{error}</div>}
      <form onSubmit={submit} className="auth-form">
        <label>Email address<input type="email" required autoComplete="email" value={form.email} onChange={e => setForm({ ...form, email: e.target.value })} placeholder="you@example.com" /></label>
        <label>Password<input type="password" required autoComplete="current-password" value={form.password} onChange={e => setForm({ ...form, password: e.target.value })} placeholder="Enter your password" /></label>
        <button className="primary wide" disabled={busy}>{busy ? 'Signing in…' : 'Sign in to Stockroom'}</button>
      </form>
      <p className="security-note">Your credentials are sent only to the LavaLust API. The browser never connects directly to Aiven.</p>
    </section>
    <aside className="login-art" aria-hidden="true">
      <div className="orb orb-one"/><div className="orb orb-two"/>
      <div className="warehouse-mark"><Icon name="box" size={84}/><span>Track every item.<br/>Keep every count.</span></div>
    </aside>
  </main>;
}

function ProductModal({ product, onClose, onSaved }) {
  const editing = Boolean(product?.id);
  const [form, setForm] = useState(product ? { ...product } : emptyProduct);
  const [errors, setErrors] = useState({});
  const [message, setMessage] = useState('');
  const [busy, setBusy] = useState(false);

  async function submit(event) {
    event.preventDefault();
    setErrors({}); setMessage(''); setBusy(true);
    try {
      const data = await request(editing ? `/products/${product.id}` : '/products', {
        method: editing ? 'PUT' : 'POST', body: JSON.stringify(form),
      });
      onSaved(data.message);
    } catch (err) {
      setErrors(err.fields || {}); setMessage(err.message);
    } finally { setBusy(false); }
  }

  const field = (name, label, type = 'text', props = {}) => <label>{label}
    <input type={type} value={form[name]} onChange={e => setForm({ ...form, [name]: e.target.value })} className={errors[name] ? 'invalid' : ''} {...props}/>
    {errors[name] && <small className="field-error">{errors[name]}</small>}
  </label>;

  return <div className="modal-backdrop" role="presentation" onMouseDown={e => e.target === e.currentTarget && onClose()}>
    <section className="modal" role="dialog" aria-modal="true" aria-labelledby="product-modal-title">
      <header><div><span className="eyebrow">Inventory record</span><h2 id="product-modal-title">{editing ? 'Edit product' : 'Add product'}</h2></div><button className="icon-button" onClick={onClose} aria-label="Close"><Icon name="close" /></button></header>
      {message && <div className="alert" role="alert"><Icon name="alert" />{message}</div>}
      <form onSubmit={submit} className="product-form">
        {field('product_name', 'Product name', 'text', { maxLength: 100, required: true, autoFocus: true })}
        <label>Description<textarea value={form.description} onChange={e => setForm({ ...form, description: e.target.value })} rows="4" className={errors.description ? 'invalid' : ''}/>{errors.description && <small className="field-error">{errors.description}</small>}</label>
        <div className="form-row">{field('price', 'Price (₱)', 'number', { min: 0, step: '0.01', required: true })}{field('quantity', 'Quantity', 'number', { min: 0, step: '1', required: true })}</div>
        <footer><button type="button" className="secondary" onClick={onClose}>Cancel</button><button className="primary" disabled={busy}>{busy ? 'Saving…' : editing ? 'Save changes' : 'Add product'}</button></footer>
      </form>
    </section>
  </div>;
}

function DeleteModal({ product, onClose, onDeleted }) {
  const [busy, setBusy] = useState(false); const [error, setError] = useState('');
  async function remove() {
    setBusy(true); setError('');
    try { const data = await request(`/products/${product.id}`, { method: 'DELETE' }); onDeleted(data.message); }
    catch (err) { setError(err.message); setBusy(false); }
  }
  return <div className="modal-backdrop"><section className="modal delete-modal" role="alertdialog" aria-modal="true" aria-labelledby="delete-title">
    <div className="danger-icon"><Icon name="trash" size={24}/></div><h2 id="delete-title">Delete this product?</h2>
    <p><strong>{product.product_name}</strong> will be permanently removed from the Aiven database.</p>
    {error && <div className="alert">{error}</div>}
    <footer><button className="secondary" onClick={onClose}>Keep product</button><button className="danger" disabled={busy} onClick={remove}>{busy ? 'Deleting…' : 'Delete product'}</button></footer>
  </section></div>;
}

function Dashboard({ user, onLogout }) {
  const [products, setProducts] = useState([]); const [loading, setLoading] = useState(true);
  const [error, setError] = useState(''); const [query, setQuery] = useState('');
  const [editing, setEditing] = useState(null); const [deleting, setDeleting] = useState(null); const [toast, setToast] = useState('');

  async function load() {
    setLoading(true); setError('');
    try { const data = await request('/products'); setProducts(data.products); }
    catch (err) { if (err.status === 401) onLogout(false); else setError(err.message); }
    finally { setLoading(false); }
  }
  useEffect(() => { load(); }, []);
  useEffect(() => { if (!toast) return; const id = setTimeout(() => setToast(''), 3000); return () => clearTimeout(id); }, [toast]);

  const filtered = useMemo(() => products.filter(p => `${p.product_name} ${p.description}`.toLowerCase().includes(query.toLowerCase())), [products, query]);
  const units = products.reduce((sum, p) => sum + p.quantity, 0);
  const value = products.reduce((sum, p) => sum + Number(p.price) * p.quantity, 0);
  async function signOut() {
    const refresh = sessionStorage.getItem('stockroom_refresh');
    try { await request('/auth/logout', { method: 'POST', body: JSON.stringify({ refresh_token: refresh }) }); } catch (_) {}
    onLogout();
  }
  function complete(message) { setEditing(null); setDeleting(null); setToast(message); load(); }

  return <div className="app-shell">
    <header className="topbar"><a className="brand" href="/app/"><span>PRODUCT</span><b>STOCKROOM</b></a><div className="account"><div className="avatar">{user.email.slice(0, 1).toUpperCase()}</div><div><strong>{user.username}</strong><span>{user.role}</span></div><button className="logout-button" onClick={signOut}><Icon name="logout"/>Logout</button></div></header>
    <main className="dashboard">
      <section className="hero"><div><span className="eyebrow">Live inventory</span><h1>Stockroom overview</h1><p>Products stored securely in Aiven and managed through the LavaLust API.</p></div><button className="primary add-button" onClick={() => setEditing({})}><Icon name="plus"/>Add product</button></section>
      <section className="stats" aria-label="Inventory summary">
        <article><span>Product records</span><strong>{products.length}</strong><small>Active catalog items</small></article>
        <article><span>Units in stock</span><strong>{units.toLocaleString()}</strong><small>Across all products</small></article>
        <article><span>Inventory value</span><strong>₱{value.toLocaleString('en-PH', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</strong><small>Price × quantity</small></article>
      </section>
      <section className="inventory-card">
        <header><div><h2>Product inventory</h2><p>{filtered.length} of {products.length} records</p></div><div className="table-tools"><label className="search"><Icon name="search"/><input value={query} onChange={e => setQuery(e.target.value)} placeholder="Search products…" aria-label="Search products"/></label><button className="icon-button" onClick={load} aria-label="Refresh"><Icon name="refresh"/></button></div></header>
        {error && <div className="alert table-alert"><Icon name="alert"/>{error}<button onClick={load}>Try again</button></div>}
        <div className="table-wrap"><table><thead><tr><th>Product</th><th>Description</th><th>Price</th><th>Quantity</th><th>Added</th><th><span className="sr-only">Actions</span></th></tr></thead><tbody>
          {loading ? <tr><td colSpan="6" className="empty">Loading inventory…</td></tr> : filtered.length === 0 ? <tr><td colSpan="6" className="empty">{products.length ? 'No products match your search.' : 'No products yet. Add the first stockroom item.'}</td></tr> : filtered.map(product => <tr key={product.id}><td><div className="product-name"><span><Icon name="box"/></span><strong>{product.product_name}</strong></div></td><td className="description-cell">{product.description || '—'}</td><td className="price">₱{Number(product.price).toLocaleString('en-PH', { minimumFractionDigits: 2 })}</td><td><span className={product.quantity < 5 ? 'stock low' : 'stock'}>{product.quantity} units</span></td><td>{product.created_at ? new Date(product.created_at.replace(' ', 'T')).toLocaleDateString('en-PH', { month: 'short', day: 'numeric', year: 'numeric' }) : '—'}</td><td><div className="row-actions"><button onClick={() => setEditing(product)} aria-label={`Edit ${product.product_name}`}><Icon name="edit"/></button><button className="delete-action" onClick={() => setDeleting(product)} aria-label={`Delete ${product.product_name}`}><Icon name="trash"/></button></div></td></tr>)}
        </tbody></table></div>
      </section>
    </main>
    {editing && <ProductModal product={editing.id ? editing : null} onClose={() => setEditing(null)} onSaved={complete}/>} {deleting && <DeleteModal product={deleting} onClose={() => setDeleting(null)} onDeleted={complete}/>} {toast && <div className="toast" role="status">{toast}</div>}
  </div>;
}

function App() {
  const [user, setUser] = useState(() => { try { return JSON.parse(sessionStorage.getItem('stockroom_user')); } catch { return null; } });
  function logout() { sessionStorage.removeItem('stockroom_access'); sessionStorage.removeItem('stockroom_refresh'); sessionStorage.removeItem('stockroom_user'); setUser(null); }
  return user ? <Dashboard user={user} onLogout={logout}/> : <Login onLogin={setUser}/>;
}

createRoot(document.getElementById('root')).render(<React.StrictMode><App /></React.StrictMode>);
