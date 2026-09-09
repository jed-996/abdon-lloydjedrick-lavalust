"""HTTP integration checks for the local Stockroom preview; never run on production."""
import os, re, sqlite3, urllib.request, urllib.parse, urllib.error, http.cookiejar
from pathlib import Path
BASE = os.environ.get('TEST_URL', 'http://127.0.0.1:8080')
if not BASE.startswith('http://127.0.0.1:'):
    raise SystemExit('This test only runs against the local preview.')
access = (Path(__file__).resolve().parents[1] / 'runtime/local-access.txt').read_text()
PASSWORD = re.search(r'^Password: (.+)$', access, re.M)[1]
EMAIL = re.search(r'^Email: (.+)$', access, re.M)[1]
class NoRedirect(urllib.request.HTTPRedirectHandler):
    def redirect_request(self, req, fp, code, msg, headers, newurl): return None
jar = http.cookiejar.CookieJar()
client = urllib.request.build_opener(urllib.request.HTTPCookieProcessor(jar), NoRedirect())
def request(path, data=None):
    payload = urllib.parse.urlencode(data).encode() if data is not None else None
    try: response = client.open(urllib.request.Request(BASE + path, payload, headers={'User-Agent':'StockroomIntegration/1.0'}))
    except urllib.error.HTTPError as error: response = error
    return response.status, response.read().decode(), response.headers
def token(html):
    match = re.search(r'name="csrf_token" value="([^"]+)"', html)
    assert match, html[:300]
    return match[1]
checks = 0
def check(condition, message):
    global checks
    assert condition, message
    checks += 1
    print('PASS', message)
for path in ['/products', '/products/create', '/products/edit/1', '/products/delete/1']:
    for data in [None, {}] if path != '/products' else [None]:
        status, body, headers = request(path, data)
        check(status == 303 and headers.get('Location','').endswith('/login'), 'Unauthenticated route protected: ' + path)
status, body, headers = request('/login')
check(status == 200 and 'Welcome back' in body, 'Login page renders')
csrf = token(body)
status, _, _ = request('/login', {'email':EMAIL,'password':PASSWORD})
check(status == 403, 'Login rejects missing CSRF')
status, _, _ = request('/login', {'email':EMAIL,'password':'incorrect','csrf_token':csrf})
check(status == 422, 'Incorrect password rejected')
old_sid = next(c.value for c in jar if c.name == 'LLSession')
status, _, headers = request('/login', {'email':EMAIL,'password':PASSWORD,'csrf_token':csrf})
check(status == 303 and headers.get('Location','').endswith('/products'), 'Correct login succeeds')
new_sid = next(c.value for c in jar if c.name == 'LLSession')
check(old_sid != new_sid, 'Session ID rotates after login')
status, body, _ = request('/products/create')
check(status == 200, 'Authenticated create form accessible')
csrf = token(body)
base = {'product_name':'Integration test product','description':'<script>alert(1)</script>','price':'1234.50','quantity':'8','csrf_token':csrf}
status, _, _ = request('/products/create', dict(base, csrf_token='invalid'))
check(status == 403, 'Create rejects invalid CSRF')
for field, value in [('product_name',''),('product_name','x'*101),('price','-1'),('price','1.234'),('price','100000000'),('quantity','-1'),('quantity','1.2'),('quantity','2147483648')]:
    status, body, _ = request('/products/create', dict(base, **{field:value}))
    check(status == 422 and 'highlighted fields' in body, 'Validation rejects ' + field + '=' + value[:15])
status, _, headers = request('/products/create', base)
check(status == 303, 'Create succeeds')
status, body, _ = request('/products')
check(status == 200 and 'Integration test product' in body and '&lt;script&gt;' in body and '<script>alert(1)</script>' not in body, 'List reads saved product and escapes HTML')
match = re.search(r'products/edit/(\d+)', body)
assert match
pid = match[1]
dbpath = Path(__file__).resolve().parents[1] / 'runtime/products.sqlite'
db = sqlite3.connect(dbpath)
row = db.execute('SELECT product_name, price, quantity, created_at FROM products WHERE id=?',(pid,)).fetchone()
check(row is not None and row[1] == 1234.5 and row[2] == 8 and row[3], 'Database persistence verified')
status, _, _ = request('/products/edit/' + pid, dict(base, csrf_token='invalid'))
check(status == 403, 'Edit rejects invalid CSRF')
status, _, _ = request('/products/edit/' + pid, dict(base, product_name='Updated integration product', price='0.00', quantity='0'))
check(status == 303, 'Update accepts zero price and quantity')
row = db.execute('SELECT product_name, price, quantity FROM products WHERE id=?',(pid,)).fetchone()
check(row == ('Updated integration product',0,0), 'Updated data persisted')
status, body, _ = request('/products/delete/' + pid)
check(status == 200 and 'Delete this product?' in body, 'Delete confirmation renders')
check(db.execute('SELECT count(*) FROM products WHERE id=?',(pid,)).fetchone()[0] == 1, 'GET delete does not remove data')
status, _, _ = request('/products/delete/' + pid, {})
check(status == 403, 'Delete rejects missing CSRF')
status, _, _ = request('/products/delete/' + pid, {'csrf_token':csrf})
check(status == 303 and db.execute('SELECT count(*) FROM products WHERE id=?',(pid,)).fetchone()[0] == 0, 'POST delete removes the product')
status, body, _ = request('/products')
check('Product deleted successfully.' in body, 'Delete success notice displayed')
status, _, _ = request('/products/edit/' + pid)
check(status == 404, 'Missing product returns 404')
status, _, _ = request('/logout', {'csrf_token':csrf})
check(status == 303, 'Logout succeeds')
status, _, headers = request('/products')
check(status == 303 and headers.get('Location','').endswith('/login'), 'Logout revokes product access')
db.close()
print(str(checks) + ' integration checks passed.')
