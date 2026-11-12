'use strict';

const assert = require('node:assert/strict');
const fs = require('node:fs');
const path = require('node:path');
const vm = require('node:vm');

const repositoryRoot = path.resolve(__dirname, '..');
const script = fs.readFileSync(path.join(repositoryRoot, 'wp-content/themes/bamero/assets/js/navigation.js'), 'utf8');

class FakeElement {
  constructor() {
    this.listeners = Object.create(null);
    this.attributes = Object.create(null);
    this.dataset = { labelOpen: 'Open menu', labelClose: 'Close menu' };
    this.classList = {
      values: new Set(),
      add(value) { this.values.add(value); },
      remove(value) { this.values.delete(value); },
      contains(value) { return this.values.has(value); },
      toggle(value, force) {
        const shouldAdd = force === undefined ? !this.values.has(value) : Boolean(force);
        if (shouldAdd) this.values.add(value);
        else this.values.delete(value);
        return shouldAdd;
      },
    };
  }

  addEventListener(type, callback) {
    this.listeners[type] = callback;
  }

  getAttribute(name) {
    return this.attributes[name] ?? null;
  }

  setAttribute(name, value) {
    this.attributes[name] = String(value);
  }

  contains(node) {
    return node === this;
  }

  getClientRects() {
    return this.visible ? [{ width: 44, height: 44 }] : [];
  }

  focus() {
    this.document.activeElement = this;
  }
}

function createHarness(toggleVisible) {
  const toggle = new FakeElement();
  const menu = new FakeElement();
  const label = { textContent: 'Open menu' };
  const menuLink = new FakeElement();
  const outsideControl = new FakeElement();
  const document = {
    activeElement: outsideControl,
    listeners: Object.create(null),
    querySelector(selector) {
      if (selector === '.menu-toggle') return toggle;
      if (selector === '#primary-navigation') return menu;
      return null;
    },
    addEventListener(type, callback) {
      this.listeners[type] = callback;
    },
  };

  toggle.document = document;
  menu.document = document;
  menuLink.document = document;
  outsideControl.document = document;
  toggle.visible = toggleVisible;
  toggle.attributes['aria-expanded'] = 'false';
  toggle.querySelector = (selector) => selector === '.screen-reader-text' ? label : null;
  menu.contains = (node) => node === menu || node === menuLink;

  vm.runInNewContext(script, { document, Element: FakeElement });

  return {
    toggle,
    menu,
    menuLink,
    outsideControl,
    document,
    pressEscape() {
      let prevented = false;
      document.listeners.keydown({
        key: 'Escape',
        preventDefault() { prevented = true; },
      });
      return prevented;
    },
    clickToggle() {
      toggle.listeners.click();
    },
  };
}

// A closed desktop menu must not steal focus when Escape is pressed.
{
  const h = createHarness(false);
  h.document.activeElement = h.outsideControl;
  assert.equal(h.pressEscape(), false);
  assert.equal(h.document.activeElement, h.outsideControl);
  assert.equal(h.toggle.getAttribute('aria-expanded'), 'false');
  assert.equal(h.menu.classList.contains('is-open'), false);
}

// Escape closes an open mobile menu only from within it and restores focus to its visible toggle.
{
  const h = createHarness(true);
  h.clickToggle();
  assert.equal(h.toggle.getAttribute('aria-expanded'), 'true');
  assert.equal(h.menu.classList.contains('is-open'), true);
  h.document.activeElement = h.menuLink;
  assert.equal(h.pressEscape(), true);
  assert.equal(h.toggle.getAttribute('aria-expanded'), 'false');
  assert.equal(h.menu.classList.contains('is-open'), false);
  assert.equal(h.document.activeElement, h.toggle);
}

// The global skip-link destination is programmatically focusable on every page template.
for (const template of ['front-page.php', 'index.php', 'page.php', 'archive.php', 'search.php', '404.php', 'single.php']) {
  const source = fs.readFileSync(path.join(repositoryRoot, 'wp-content/themes/bamero', template), 'utf8');
  assert.match(source, /<main\s+id="main-content"\s+tabindex="-1"/, `${template} must expose a focusable skip-link target`);
  assert.equal((source.match(/<h1\b/g) || []).length, 1, `${template} must provide one static page-level H1 heading`);
}

const functionsSource = fs.readFileSync(path.join(repositoryRoot, 'wp-content/themes/bamero/functions.php'), 'utf8');
assert.ok(functionsSource.includes("add_theme_support( 'title-tag' );"), 'Theme must delegate document titles to WordPress core');
assert.ok(functionsSource.includes("remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );"), 'Theme must replace WooCommerce archive/product start wrapper');
assert.ok(functionsSource.includes("remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );"), 'Theme must replace WooCommerce archive/product end wrapper');
assert.ok(functionsSource.includes("add_action( 'woocommerce_before_main_content', 'bamero_woocommerce_wrapper_start', 10 );"), 'Theme must register its main-content start wrapper');
assert.ok(functionsSource.includes("add_action( 'woocommerce_after_main_content', 'bamero_woocommerce_wrapper_end', 10 );"), 'Theme must register its main-content end wrapper');
assert.ok(functionsSource.includes('<main id="main-content" tabindex="-1" class="content-width page-content site-main" role="main">'), 'WooCommerce wrapper must expose the shared focusable main landmark');
assert.match(functionsSource, /add_filter\( 'woocommerce_breadcrumb_defaults', 'bamero_breadcrumb_defaults' \);/, 'Theme must customize the existing WooCommerce breadcrumb through its documented filter');
assert.match(functionsSource, /str_replace\(\s*'aria-label="Breadcrumb"'/, 'Theme must preserve custom breadcrumb wrappers and replace only WooCommerce’s default label');
assert.match(functionsSource, /aria-label="' \. esc_attr__\( 'مسیر راهنما', 'bamero' \) \. '"'/, 'WooCommerce breadcrumb landmark must have a safely escaped Persian accessible name');
assert.doesNotMatch(functionsSource, /remove_action\(\s*'woocommerce_before_main_content'\s*,\s*'woocommerce_breadcrumb'/, 'Theme must leave WooCommerce breadcrumb output enabled');

const archiveSource = fs.readFileSync(path.join(repositoryRoot, 'wp-content/themes/bamero/archive.php'), 'utf8');
assert.match(archiveSource, /echo wp_kses_post\(\s*get_the_archive_title\(\)\s*\);/, 'Archive title must preserve safe WordPress markup while sanitizing output');
assert.doesNotMatch(archiveSource, /echo esc_html\(\s*get_the_archive_title\(\)\s*\);/, 'Archive title markup must not be rendered as visible escaped tags');
const headerSource = fs.readFileSync(path.join(repositoryRoot, 'wp-content/themes/bamero/header.php'), 'utf8');
assert.match(headerSource, /<head>[\s\S]*?wp_head\(\);[\s\S]*?<\/head>/, 'Header must run WordPress head hooks for title and site metadata');
assert.doesNotMatch(headerSource, /<title\b/i, 'Theme must not print a second hard-coded title tag');
assert.match(headerSource, /<div class="brand">[\s\S]*?the_custom_logo\(\);[\s\S]*?<\/div>/, 'Custom logo must sit inside the mobile-grid brand wrapper');
assert.doesNotMatch(headerSource, /<a class="brand"[^>]*>[\s\S]*?the_custom_logo\(\);/, 'WordPress custom logo must not be nested inside a second home link');
assert.match(headerSource, /<a class="brand-link"/, 'Fallback brand text must keep its own home link');
const stylesheetSource = fs.readFileSync(path.join(repositoryRoot, 'wp-content/themes/bamero/style.css'), 'utf8');
assert.match(stylesheetSource, /\.brand\s*\{\s*grid-area:\s*brand;/, 'The brand wrapper must remain the named mobile grid item');
for (const columns of [1, 2, 3, 4]) {
  assert.match(stylesheetSource, new RegExp(`ul\\.products\\.columns-${columns}\\s*\\{[^}]*grid-template-columns:\\s*repeat\\(${columns},`), `WooCommerce columns-${columns} setting must be honored on wide screens`);
}
assert.match(stylesheetSource, /ul\.products:not\(\.columns-1\)\s*\{\s*grid-template-columns:\s*repeat\(2,/, 'Responsive grid may collapse multi-column loops to two columns but must preserve a selected single column');
const indexSource = fs.readFileSync(path.join(repositoryRoot, 'wp-content/themes/bamero/index.php'), 'utf8');
assert.match(indexSource, /get_option\(\s*'page_for_posts'\s*\)/, 'Blog index must use its assigned posts-page title when configured');
assert.match(indexSource, /get_bloginfo\(\s*'name'\s*\)/, 'Blog index must fall back to the configured site name');
assert.match(indexSource, /<h2 class="entry-title">/, 'Blog-list item titles must be subordinate to the page heading');
assert.doesNotMatch(indexSource, /<h1 class="entry-title">/, 'Repeated post cards must not each become page-level H1 headings');
const notFoundSource = fs.readFileSync(path.join(repositoryRoot, 'wp-content/themes/bamero/404.php'), 'utf8');
assert.match(notFoundSource, /این صفحه پیدا نشد/, '404 template must provide a clear not-found message');
assert.match(notFoundSource, /get_header\(\)[\s\S]*get_footer\(\)/, 'Custom 404 template must preserve the site shell');
const frontPageSource = fs.readFileSync(path.join(repositoryRoot, 'wp-content/themes/bamero/front-page.php'), 'utf8');
assert.match(frontPageSource, /\$category_url\s*=\s*get_term_link\(\s*\$category\s*\);[\s\S]*?is_wp_error\(\s*\$category_url\s*\)/, 'Category links must handle WP_Error before escaping');

console.log('PASS: accessibility, WooCommerce wrapper/breadcrumb/grid settings, title-tag, heading hierarchy, 404 view, logo markup, and output-safety contracts.');
