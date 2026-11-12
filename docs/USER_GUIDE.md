# Rakiza Tech — Website Guide

This guide explains how to manage the Rakiza Tech website from its **dashboard**: adding projects and websites, editing texts, reading messages, managing your team and more. No technical knowledge is needed.

> You can read this guide any time inside the dashboard: click **Help & guide** at the bottom of the left menu.

## 1. Signing in

1. Open your website's address and add **/admin** at the end — for example `https://yourdomain.com/admin`.
2. Type your **email** and **password**, then click **Sign in**.
3. Tick **Keep me signed in** on your own computer if you do not want to sign in every time.

**Forgot your password?** Click **Forgot your password?** on the sign-in page, type your email and press the button. You will receive an email with a link (valid for 60 minutes). Open it, choose a new password and sign in.

Good to know:

- After **5 wrong attempts** the sign-in is paused for one minute. Wait and try again.
- If you see *"This account is deactivated"*, an administrator has switched your account off. Ask them to switch it back on.
- To **sign out**, click your initials in the top-right corner and choose **Sign out**.

## 2. The dashboard at a glance

- **Left menu** — every area you are allowed to use. You only see what your role allows (see *Roles & permissions*).
  - **Content**: Projects, Websites, Services, Skill categories, Skills, Numbers.
  - **Inbox**: Messages (a number shows how many are unread).
  - **Administration**: Users, Roles, Settings, Activity.
- **Top bar** — the page name, the **color theme** button (the half-filled circle) and your **account menu** (My profile, Help, Sign out).
- **Dashboard home** — how many items you have and how many are published, the newest messages, recent changes, and quick buttons such as *New project*.

On a phone, open the menu with the **☰** button in the top-left corner.

## 3. Working with lists

Projects, Websites, Services, Skill categories, Skills and Numbers all work the same way.

| What you want | How |
|---|---|
| Find something | Type in the **search box** and click **Filter**. Use the **All / Published / Drafts** menu to narrow the list. **Clear** shows everything again. |
| Add a new item | Click the **New …** button at the top right. |
| Edit an item | Click its name, or the **pencil** icon. |
| See it on the website | Click the **eye** icon (opens in a new tab). |
| Hide or show it on the website | Click its **published / draft** badge. Drafts are only visible to your team. |
| Change the order on the website | Drag the row by its **⋮⋮ handle**, or use the small **↑ ↓ arrows**. The new order is saved immediately ("New order saved."). |
| Delete it | Click the **bin** icon and confirm. **Deleting cannot be undone** — if you are unsure, switch it to *draft* instead. |

> The order of the list is the order on the website. Searching or filtering hides the drag handles; click **Clear** to reorder.

## 4. Projects

Projects are your case studies. They appear in **"Selected work"** on the home page, on the **/projects** page, and each one gets its own page at `/projects/its-name`.

| Field | What to write |
|---|---|
| **Title** | The project's name, e.g. *Garage Parking System*. |
| **URL slug** | The end of the page address. Leave it empty and it is made from the title (*garage-parking-system*). |
| **Subtitle** | One line under the title, e.g. *Parking management platform*. |
| **Client**, **Year** | Optional. |
| **Tech stack** | Technologies separated by commas: `Laravel, MySQL, Tailwind`. The first one is highlighted. |
| **Summary** | Two or three sentences. Shown at the top of the project page and in Google results. |
| **Full write-up** | The story of the project (see *Formatting text* below). |
| **Card artwork** | The drawing on the project card: dashboard, store page, task board, terminal, live map — or **Uploaded image** to show your cover screenshot. |
| **Cover image** | A screenshot (JPG, PNG or WebP, up to 4 MB). Also used when someone shares the project link. To delete it, tick **Remove this image** and save. |
| **Live site**, **Source code** | Full links starting with `https://`. They become buttons on the project page. |
| **SEO title / description** | Optional. Leave empty to use the title and summary. |
| **Show on the home page** | Off = the project is only listed on /projects. |
| **Published** | Off = draft: only your team can open it (with a *Draft preview* banner). |

### Formatting text

The **Full write-up** (and the About text in Settings) understands simple formatting:

| You type | You get |
|---|---|
| `## The challenge` | A heading |
| `- Fast check-in` | A bullet point (one per line) |
| `**important**` | **important** |
| `*nice*` | *nice* |
| `[our site](https://example.com)` | a link |
| An empty line | A new paragraph |

## 5. Websites

Websites are client sites you launched. They appear in the **"Websites we launched"** section of the home page — the section only shows once at least one website is **published**.

| Field | What to write |
|---|---|
| **Name** | E.g. *Nile Dental Clinic*. |
| **Address (URL)** | The full address, starting with `https://`. The *Visit site* button opens it. |
| **Client**, **Category** | Optional, e.g. *E-commerce*, *Clinic*. |
| **Status** | Live, In development or Archived (shown as a small badge). |
| **Launch date** | Optional. |
| **Short description** | One or two sentences. |
| **Screenshot** | An image of the site's home page. A wide image (about 1600 × 1000 px) looks best. Without one, the first letter of the name is shown. |

## 6. Services

Services are the cards in **"What we build"**. Each has a **title**, a **description**, **tags** (comma separated) and an **icon & animation** (code editor, phone app, bar chart, database table or command line). Switch on **Wide card** to make a card twice as wide on large screens — the first card is wide by default.

## 7. Skills & the Stack section

The **Stack** section has two parts: two scrolling rows of technology names, and cards grouped by category (Frontend, Backend, …).

1. **Skill categories** are the cards. Each has a **name**, a **small label** (e.g. *UI / UX*) and an **icon**. Deleting a category also deletes its skills.
2. **Skills** belong to a category. For each skill you choose:
   - **Level (0–100)** — fill it in to show a progress bar in the card; leave empty for no bar.
   - **Scrolling row** — top row, bottom row, or not in the scrolling rows.
   - **Show as a tag in its category card** — on or off.

> The **Proficiency** circles in the "By the numbers" dashboard show the average level of the first two categories, so they update by themselves.

## 8. Numbers

Numbers appear in two places, chosen with **Where it appears**:

- **Highlights** — the stats under the hero (e.g. *6+ years*) and in the About section (*6+ Years experience*). The **Short label** is the one-word version used under the hero.
- **Dashboard cards** — the cards of the **"By the numbers"** section, with a **corner tag** (e.g. *YTD*), a **note** (e.g. *+12.4% this month*), a **trend arrow** and a **line color**.

**Number**, **Decimals**, **Before the number** and **After the number** control how it looks: `12400` with `+` after it shows as **12,400+**; `99.98` with 2 decimals and `%` shows as **99.98%**. Numbers count up when visitors scroll to them.

## 9. Messages

Everything visitors send with the contact form arrives in **Messages**.

- **Unread** messages are highlighted and counted in the menu.
- Opening a message marks it as read. Use **Mark as unread** to keep it on your to-do list, or **Mark all as read** on the list.
- **Reply by email** opens your email program with the visitor's address filled in.
- **Delete** removes a message permanently.

**Email notifications:** under **Settings → Contact & social**, switch on *Email me when a contact message arrives* to also get each message by email at your main address (your developer must set up email sending once).

Spam protection is built in: the form blocks robots and allows at most 5 messages per 10 minutes from the same visitor.

## 10. Settings

Settings has four tabs. Change what you need and click **Save** at the bottom of the tab.

- **General** — company name, Arabic name (the animated name in the menu switches between both), tagline (used in the browser tab title), availability status (the small green status; leave empty to hide it), location and the **default color theme** for first-time visitors.
- **Contact & social** — main and second email, phone, email notifications, and links to GitHub, LinkedIn, X, Facebook and Instagram. **Empty links are hidden** on the website.
- **Home page texts** — the headings and intros of every section: the hero headline and highlighted word, the typed phrases (**one per line**), the About text (with formatting), and the footer lines.
- **SEO** — how the site looks in Google and when shared: page title, description, the **social sharing image** (1200 × 630 px is ideal), your Google Search Console verification code and an optional Google Analytics ID.

## 11. Users

**Users** lists everyone who can sign in, their role, whether their account is switched on, and when they last signed in.

- **New user**: fill in name, email, a password (at least 8 characters with letters and numbers), the **role**, and whether the account is switched on. Share the password privately; they can change it under *My profile*.
- **Edit** a user to change their details, role or password, or to **switch the account off** (they can no longer sign in, but nothing is deleted).
- **Delete** removes the account.

Safety rules (they protect you from accidents):

- Nobody can change **their own role**, switch **themselves** off or delete **their own** account.
- Only a **Super Admin** can give the Super Admin role or change a Super Admin's account.
- The **last active Super Admin** can never be demoted, switched off or deleted.

## 12. Roles & permissions

A **role** is a set of permissions, and every user has one role. Three roles come ready:

| Role | Can do |
|---|---|
| **Super Admin** | Everything, always. This role is protected and cannot be edited or deleted. |
| **Editor** | Add, edit, publish and delete all content (projects, websites, services, skills, numbers), handle messages and read the activity log. Cannot manage users, roles or settings. |
| **Viewer** | Look at the content and read messages, without changing anything. |

To make your own role, open **Roles → New role**, give it a name and tick the permissions in the table:

- **View** — see the list. **Create** — add new items. **Edit** — change existing items and their order. **Delete** — remove items.
- **Publish** — make content visible on the website. People without it can still write **drafts** for someone else to publish.
- The **All** box ticks a whole row at once.

A role that is still given to users cannot be deleted — move those users to another role first.

## 13. Color themes

Visitors can choose how the website looks with the **theme button** (the half-filled circle) in the menu:

| Theme | Best for |
|---|---|
| **System** | Follows the visitor's device (dark or light), and switches automatically. |
| **Dark** | The original look. |
| **Light** | Bright rooms and daytime. |
| **Dim** | A softer dark with lower contrast — easier on the eyes. |
| **Sepia** | Warm paper colors with less blue light — comfortable at night. |
| **Rakiza** | The brand colors: deep green and gold, like the logo. |

The choice is remembered on that device. The dashboard has the same button. To change what **first-time visitors** see, go to **Settings → General → Default color theme**.

## 14. Activity log

**Activity** lists every change made in the dashboard — who created, edited, published, reordered or deleted what, settings changes, and sign-ins — newest first. Use the search box or the person filter to find something. It is read-only.

## 15. My profile

Click your initials (top right) → **My profile** to change your name, email or password. Changing your **email or password** asks for your **current password**, for your security.

## 16. Getting found on Google

The website is already built for search engines: fast pages, a clear structure, a sitemap, descriptions and sharing images. You make the biggest difference with good content:

1. **Write a real summary for every project** — two or three sentences saying what you built and for whom. It becomes the Google description.
2. **Use clear titles** — *"Clinic Booking System"* is better than *"Project 3"*.
3. **Add cover images and screenshots** — they make shared links attractive.
4. **Keep Settings → SEO up to date** — a description between 120 and 160 characters works best.
5. **Connect Google Search Console** (free): add your site at search.google.com/search-console, choose the *HTML tag* method, copy the code inside `content="…"` into **Settings → SEO → Google Search Console verification code**, save, and click *Verify*. Then submit your sitemap: `https://yourdomain.com/sitemap.xml`.
6. **Publish regularly** — new projects and websites show Google the site is alive.

## 17. Questions & answers

**I changed something but the website still shows the old version.**
Refresh the website page (Ctrl + F5 / Cmd + Shift + R). Check that the item is **published**, and for projects that **Show on the home page** is on.

**A whole section disappeared from the home page.**
Sections with nothing published are hidden automatically (for example *Websites we launched* or *Selected work*). Publish at least one item.

**My image will not upload.**
Images must be JPG, PNG or WebP and smaller than 4 MB. Make it smaller with a free tool such as squoosh.app and try again.

**I am not receiving emails for new messages.**
Check *Settings → Contact & social* (notifications on, correct main email). If it still fails, your developer needs to set up email sending. Messages are always saved in the dashboard anyway.

**I see "This page expired".**
The page was open for a long time. Go back, refresh the page and try again.

**Nobody can sign in any more.**
Use *Forgot your password?* first. If that does not help, your developer can create a new admin account with one command (see the developer guide).
