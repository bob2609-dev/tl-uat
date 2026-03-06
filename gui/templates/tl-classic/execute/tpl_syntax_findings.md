# TPL Syntax Error Report
**File:** `inc_exec_show_tc_exec.tpl`  
**Date:** 2026-03-04

---

## Summary

Seven issues were identified in the template file, ranging from critical compilation errors to minor code quality concerns.

---

## Issues

### 1. Mismatched `{foreach}` / `{/foreach}` Blocks — *Critical*

There are two `{foreach}` loops whose closing tags are misaligned. The inner `{foreach item=tc_old_exec ...}` closes with `{/foreach}` deep inside a block that also contains the outer loop's closing content. The `</table>` and surrounding `{/if}` blocks appear *after* the inner `{/foreach}`, but the outer `{/foreach}` is never properly closed before the final `</div>` tags.

---

### 2. Unclosed `{if}` Block — *Critical*

In the `show_last_exec_any_build` section, the outer `{if}` condition is never closed. The block flows directly into the next `{if $drawNotRun}` without a corresponding `{/if}`:

```smarty
{if $cfg->exec_cfg->show_last_exec_any_build && $gui->history_on == 0}
  ...
  {if $abs_last_exec.status != '' and ...}
    ...
  {else}
    {$drawNotRun=1}
  {/if}
{* ← missing {/if} for outer block *}
```

---

### 3. Orphaned `</a>` Closing Tags — *High*

In the issue tracker integration block, two `<img>` elements are followed by stray `</a>` closing tags with no matching opening `<a>` tag:

```html
<img src="{$tlImages.bug_link_tl_to_bts_disabled}" style="border:none" /></a>
<img src="{$tlImages.bug_create_into_bts_disabled}" title="..." style="border:none" /></a>
```

These produce invalid HTML and may break layout or accessibility tools.

---

### 4. `{literal}` / `{/literal}` Misuse in `panel_init` Blocks — *High*

A Smarty variable is referenced outside the `{/literal}` close tag but inside a JS string that was opened within a `{literal}` block. This pattern appears twice and will cause the variable to render as a literal string rather than its resolved value:

```smarty
renderTo:'exec_notes_container_{$tc_old_exec.execution_id}'{literal},
```

The `{literal}` tag opens *after* this line, but the prior block's `{/literal}` is not consistently closed before reuse, making the scoping ambiguous.

---

### 5. `$execID` Reassigned Mid-Loop — *Medium*

`$execID` is assigned from `$tc_exec.execution_id` twice within the outer `{foreach}` — once near the top and again near the bottom — potentially overwriting the value mid-render depending on the Smarty version in use:

```smarty
{$execID=$tc_exec.execution_id}   ← first assignment
...
{$execID=$tc_old_exec.execution_id}  ← overwritten mid-loop
```

Use distinct variable names for each context to avoid ambiguity.

---

### 6. `is_array()` Used Directly in Smarty Template — *Critical*

`is_array()` is a native PHP function and is **not valid Smarty syntax** in standard Smarty 2/3. This line will throw a template compilation error unless a custom plugin or modifier has been explicitly registered:

```smarty
{if isset($gui->bugs) && is_array($gui->bugs) && isset($gui->bugs[$execID])}
```

**Fix:** Move this logic into the PHP controller and pass a pre-evaluated boolean to the template, or register `is_array` as a custom Smarty function.

---

### 7. Debug Artifacts Left in Production Template — *Low*

Several debug elements were found that will render on every loop iteration in production:

- An `<!-- DEBUG: ... -->` HTML comment inside `{foreach item=tc_old_exec ...}`
- An inline `<script>` with `console.log(...)` inside the same loop
- A fixed-position `<div>` at the top of the file with `display:none` and debug output, including a `document.write(new Date())` call

These should be removed before deployment.

---

## Issue Summary Table

| # | Issue | Severity |
|---|-------|----------|
| 1 | Mismatched `{foreach}` / `{/foreach}` blocks | Critical |
| 2 | Unclosed `{if}` block | Critical |
| 3 | Orphaned `</a>` closing tags | High |
| 4 | `{literal}` / `{/literal}` misuse | High |
| 5 | `$execID` reassigned mid-loop | Medium |
| 6 | `is_array()` used in Smarty template | Critical |
| 7 | Debug artifacts left in template | Low |
