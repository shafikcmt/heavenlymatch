/// <reference path="../../../types/ziggy.d.ts" />
import { useState } from 'react'
import { Head, router, usePage } from '@inertiajs/react'
import AdminLayout from '@/layouts/AdminLayout'
import { Button } from '@/components/ui/Button'
import { Input } from '@/components/ui/Input'
import { useTranslation } from '@/lib/i18n'
import { cn } from '@/lib/utils'
import {
  Plus, Pencil, Trash2, ChevronUp, ChevronDown, Lock, X,
  Eye, EyeOff, Star, Filter, Search as SearchIcon,
} from 'lucide-react'

/* ── Types ─────────────────────────────────────────────────────────────── */

interface FieldOption { value: string; label: string; [key: string]: string }
interface ConditionalLogic { field?: string; operator?: string; value?: string | number | null }

interface Field {
  id: number
  section_id: number
  field_key: string
  model_column: string | null
  is_custom: boolean
  label_en: string
  label_bn: string
  placeholder_en: string | null
  placeholder_bn: string | null
  helper_text_en: string | null
  helper_text_bn: string | null
  input_type: string
  options_en: FieldOption[]
  options_bn: FieldOption[]
  default_value: string | null
  validation_rules: string | null
  is_required: boolean
  is_active: boolean
  show_in_form: boolean
  show_in_profile: boolean
  show_in_admin: boolean
  is_private: boolean
  is_searchable: boolean
  is_filterable: boolean
  is_system: boolean
  sort_order: number
  conditional_logic: ConditionalLogic | null
}

interface Section {
  id: number
  key: string
  title_en: string
  title_bn: string
  description_en: string | null
  description_bn: string | null
  icon: string | null
  step: number | null
  sort_order: number
  completion_weight: number
  is_active: boolean
  show_in_form: boolean
  show_in_profile: boolean
  show_in_admin: boolean
  has_system_fields: boolean
  fields: Field[]
}

interface Props {
  sections: Section[]
  inputTypes: string[]
}

const OPTION_TYPES = ['select', 'multi_select', 'radio']

/* ── Page ──────────────────────────────────────────────────────────────── */

export default function BiodataFieldsIndex({ sections, inputTypes }: Props) {
  const { t } = useTranslation()
  const errors = (usePage().props.errors ?? {}) as Record<string, string>

  const [sectionModal, setSectionModal] = useState<Section | 'new' | null>(null)
  const [fieldModal, setFieldModal] = useState<{ field: Field | null; sectionId: number } | null>(null)

  const moveSection = (index: number, dir: -1 | 1) => {
    const j = index + dir
    if (j < 0 || j >= sections.length) return
    const ids = sections.map(s => s.id)
    const tmp = ids[index]!; ids[index] = ids[j]!; ids[j] = tmp
    router.post(route('admin.biodata-fields.sections.reorder'), { ids }, { preserveScroll: true })
  }

  const moveField = (section: Section, index: number, dir: -1 | 1) => {
    const j = index + dir
    if (j < 0 || j >= section.fields.length) return
    const ids = section.fields.map(f => f.id)
    const tmp = ids[index]!; ids[index] = ids[j]!; ids[j] = tmp
    router.post(route('admin.biodata-fields.fields.reorder'),
      { section_id: section.id, ids }, { preserveScroll: true })
  }

  const deleteSection = (s: Section) => {
    if (!confirm(t('admin', 'bf_confirm_delete_section'))) return
    router.delete(route('admin.biodata-fields.sections.destroy', { section: s.id }), { preserveScroll: true })
  }

  const deleteField = (f: Field) => {
    if (!confirm(t('admin', 'bf_confirm_delete_field'))) return
    router.delete(route('admin.biodata-fields.fields.destroy', { field: f.id }), { preserveScroll: true })
  }

  return (
    <AdminLayout>
      <Head title={t('admin', 'bf_title')} />

      <div className="mx-auto max-w-5xl">
        <div className="mb-6 flex items-start justify-between gap-4">
          <div>
            <h1 className="text-xl font-bold text-slate-900">{t('admin', 'bf_title')}</h1>
            <p className="mt-1 text-sm text-slate-500">{t('admin', 'bf_subtitle')}</p>
          </div>
          <Button onClick={() => setSectionModal('new')} className="shrink-0">
            <Plus size={16} /> {t('admin', 'bf_add_section')}
          </Button>
        </div>

        <div className="space-y-4">
          {sections.map((section, sIdx) => (
            <div key={section.id} className="rounded-2xl border border-slate-200 bg-white shadow-sm">
              {/* Section header */}
              <div className="flex items-center gap-3 border-b border-slate-100 px-4 py-3">
                <div className="flex flex-col">
                  <button disabled={sIdx === 0} onClick={() => moveSection(sIdx, -1)}
                    className="text-slate-400 hover:text-slate-700 disabled:opacity-30">
                    <ChevronUp size={14} />
                  </button>
                  <button disabled={sIdx === sections.length - 1} onClick={() => moveSection(sIdx, 1)}
                    className="text-slate-400 hover:text-slate-700 disabled:opacity-30">
                    <ChevronDown size={14} />
                  </button>
                </div>

                <div className="min-w-0 flex-1">
                  <div className="flex items-center gap-2">
                    <span className="font-semibold text-slate-900">{section.title_en}</span>
                    <code className="rounded bg-slate-100 px-1.5 py-0.5 text-xs text-slate-500">{section.key}</code>
                    {section.step != null && (
                      <span className="text-xs text-slate-400">{t('admin', 'bf_step')} {section.step}</span>
                    )}
                    {!section.is_active && (
                      <span className="rounded bg-slate-200 px-1.5 py-0.5 text-xs text-slate-600">
                        {t('admin', 'bf_inactive')}
                      </span>
                    )}
                  </div>
                  <p className="truncate text-xs text-slate-400">{section.title_bn}</p>
                </div>

                <span className="hidden text-xs text-slate-400 sm:inline">
                  {section.fields.length} {t('admin', 'bf_fields')}
                </span>

                <button onClick={() => setSectionModal(section)}
                  className="rounded-lg p-2 text-slate-400 hover:bg-slate-100 hover:text-slate-700">
                  <Pencil size={15} />
                </button>
                <button
                  onClick={() => deleteSection(section)}
                  disabled={section.has_system_fields}
                  title={section.has_system_fields ? t('admin', 'bf_section_locked') : ''}
                  className="rounded-lg p-2 text-red-400 hover:bg-red-50 hover:text-red-600 disabled:cursor-not-allowed disabled:opacity-30">
                  {section.has_system_fields ? <Lock size={15} /> : <Trash2 size={15} />}
                </button>
              </div>

              {/* Fields */}
              <div className="divide-y divide-slate-50">
                {section.fields.map((field, fIdx) => (
                  <div key={field.id} className="flex items-center gap-2 px-4 py-2.5 hover:bg-slate-50/60">
                    <div className="flex flex-col">
                      <button disabled={fIdx === 0} onClick={() => moveField(section, fIdx, -1)}
                        className="text-slate-300 hover:text-slate-600 disabled:opacity-20">
                        <ChevronUp size={12} />
                      </button>
                      <button disabled={fIdx === section.fields.length - 1} onClick={() => moveField(section, fIdx, 1)}
                        className="text-slate-300 hover:text-slate-600 disabled:opacity-20">
                        <ChevronDown size={12} />
                      </button>
                    </div>

                    <div className="min-w-0 flex-1">
                      <div className="flex flex-wrap items-center gap-1.5">
                        <span className={cn('text-sm font-medium', field.is_active ? 'text-slate-800' : 'text-slate-400 line-through')}>
                          {field.label_en}
                        </span>
                        {field.is_required && <span className="text-red-500">*</span>}
                        <code className="rounded bg-slate-100 px-1.5 py-0.5 text-[11px] text-slate-500">{field.field_key}</code>
                        <span className="rounded bg-indigo-50 px-1.5 py-0.5 text-[11px] text-indigo-600">{field.input_type}</span>
                        {field.is_system
                          ? <span className="rounded bg-amber-50 px-1.5 py-0.5 text-[11px] text-amber-700">{t('admin', 'bf_system')}</span>
                          : <span className="rounded bg-emerald-50 px-1.5 py-0.5 text-[11px] text-emerald-700">{t('admin', 'bf_custom')}</span>}
                      </div>
                    </div>

                    {/* Quick flags */}
                    <div className="hidden items-center gap-1 text-slate-300 md:flex">
                      {!field.show_in_form && <span title={t('admin', 'bf_hidden_form')}><EyeOff size={13} /></span>}
                      {field.show_in_profile ? <span title={t('admin', 'bf_show_profile')}><Eye size={13} className="text-slate-400" /></span> : null}
                      {field.is_private && <span title={t('admin', 'bf_private')}><Lock size={13} className="text-slate-400" /></span>}
                      {field.is_filterable && <span title={t('admin', 'bf_filterable')}><Filter size={13} className="text-slate-400" /></span>}
                      {field.is_searchable && <span title={t('admin', 'bf_searchable')}><SearchIcon size={13} className="text-slate-400" /></span>}
                      {field.conditional_logic?.field && <span title={t('admin', 'bf_conditional')}><Star size={13} className="text-slate-400" /></span>}
                    </div>

                    <button onClick={() => setFieldModal({ field, sectionId: section.id })}
                      className="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 hover:text-slate-700">
                      <Pencil size={14} />
                    </button>
                    <button
                      onClick={() => deleteField(field)}
                      disabled={field.is_system}
                      title={field.is_system ? t('admin', 'bf_field_locked') : ''}
                      className="rounded-lg p-1.5 text-red-400 hover:bg-red-50 hover:text-red-600 disabled:cursor-not-allowed disabled:opacity-30">
                      {field.is_system ? <Lock size={14} /> : <Trash2 size={14} />}
                    </button>
                  </div>
                ))}

                <div className="px-4 py-2.5">
                  <button onClick={() => setFieldModal({ field: null, sectionId: section.id })}
                    className="flex items-center gap-1.5 text-sm font-medium text-primary-600 hover:text-primary-700">
                    <Plus size={14} /> {t('admin', 'bf_add_field')}
                  </button>
                </div>
              </div>
            </div>
          ))}
        </div>
      </div>

      {sectionModal && (
        <SectionModal
          section={sectionModal === 'new' ? null : sectionModal}
          errors={errors}
          onClose={() => setSectionModal(null)}
        />
      )}
      {fieldModal && (
        <FieldModal
          field={fieldModal.field}
          sectionId={fieldModal.sectionId}
          inputTypes={inputTypes}
          errors={errors}
          onClose={() => setFieldModal(null)}
        />
      )}
    </AdminLayout>
  )
}

/* ── Shared bits ───────────────────────────────────────────────────────── */

function Toggle({ checked, onChange, label }: { checked: boolean; onChange: (v: boolean) => void; label: string }) {
  return (
    <button type="button" onClick={() => onChange(!checked)}
      className="flex items-center gap-2 text-sm text-slate-700">
      <span className={cn('relative h-5 w-9 rounded-full transition-colors', checked ? 'bg-primary-600' : 'bg-slate-300')}>
        <span className={cn('absolute top-0.5 h-4 w-4 rounded-full bg-white transition-transform', checked ? 'translate-x-4' : 'translate-x-0.5')} />
      </span>
      {label}
    </button>
  )
}

function ModalShell({ title, onClose, onSubmit, processing, errors, children }: {
  title: string
  onClose: () => void
  onSubmit: () => void
  processing: boolean
  errors: Record<string, string>
  children: React.ReactNode
}) {
  const { t } = useTranslation()
  const errorList = Object.values(errors)
  return (
    <div className="fixed inset-0 z-[60] flex items-end justify-center bg-black/40 p-0 sm:items-center sm:p-4" onClick={onClose}>
      <div className="flex max-h-[92vh] w-full max-w-lg flex-col rounded-t-2xl bg-white sm:rounded-2xl" onClick={e => e.stopPropagation()}>
        <div className="flex items-center justify-between border-b border-slate-100 px-5 py-3">
          <h2 className="font-semibold text-slate-900">{title}</h2>
          <button onClick={onClose} className="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100"><X size={18} /></button>
        </div>
        <div className="flex-1 space-y-4 overflow-y-auto px-5 py-4">
          {errorList.length > 0 && (
            <div className="rounded-xl bg-red-50 border border-red-200 px-3 py-2 text-xs text-red-700">
              {errorList.map((e, i) => <div key={i}>{e}</div>)}
            </div>
          )}
          {children}
        </div>
        <div className="flex justify-end gap-2 border-t border-slate-100 px-5 py-3">
          <Button variant="outline" onClick={onClose}>{t('admin', 'bf_cancel')}</Button>
          <Button onClick={onSubmit} isLoading={processing}>{t('admin', 'bf_save')}</Button>
        </div>
      </div>
    </div>
  )
}

/* ── Section modal ─────────────────────────────────────────────────────── */

function SectionModal({ section, errors, onClose }: {
  section: Section | null
  errors: Record<string, string>
  onClose: () => void
}) {
  const { t } = useTranslation()
  const [processing, setProcessing] = useState(false)
  const [form, setForm] = useState({
    key: section?.key ?? '',
    title_en: section?.title_en ?? '',
    title_bn: section?.title_bn ?? '',
    description_en: section?.description_en ?? '',
    description_bn: section?.description_bn ?? '',
    icon: section?.icon ?? '',
    step: section?.step ?? ('' as number | ''),
    completion_weight: section?.completion_weight ?? 10,
    is_active: section?.is_active ?? true,
    show_in_form: section?.show_in_form ?? true,
    show_in_profile: section?.show_in_profile ?? true,
    show_in_admin: section?.show_in_admin ?? true,
  })
  const set = <K extends keyof typeof form>(k: K, v: (typeof form)[K]) => setForm(f => ({ ...f, [k]: v }))

  const submit = () => {
    setProcessing(true)
    const opts = { preserveScroll: true, onSuccess: onClose, onFinish: () => setProcessing(false) }
    if (section) router.put(route('admin.biodata-fields.sections.update', { section: section.id }), form, opts)
    else router.post(route('admin.biodata-fields.sections.store'), form, opts)
  }

  return (
    <ModalShell
      title={section ? t('admin', 'bf_edit_section') : t('admin', 'bf_add_section')}
      onClose={onClose} onSubmit={submit} processing={processing} errors={errors}
    >
      <div className="grid grid-cols-2 gap-3">
        <Input label={t('admin', 'bf_title_en')} value={form.title_en} required
          onChange={e => set('title_en', e.target.value)} />
        <Input label={t('admin', 'bf_title_bn')} value={form.title_bn} required
          onChange={e => set('title_bn', e.target.value)} />
      </div>
      {section && (
        <Input label={t('admin', 'bf_key')} value={form.key} disabled
          helperText={t('admin', 'bf_key_immutable')} />
      )}
      <div className="grid grid-cols-2 gap-3">
        <Input label={t('admin', 'bf_desc_en')} value={form.description_en}
          onChange={e => set('description_en', e.target.value)} />
        <Input label={t('admin', 'bf_desc_bn')} value={form.description_bn}
          onChange={e => set('description_bn', e.target.value)} />
      </div>
      <div className="grid grid-cols-3 gap-3">
        <Input label={t('admin', 'bf_icon')} value={form.icon} onChange={e => set('icon', e.target.value)} />
        <Input label={t('admin', 'bf_step')} type="number" value={form.step}
          onChange={e => set('step', e.target.value === '' ? '' : Number(e.target.value))} />
        <Input label={t('admin', 'bf_weight')} type="number" value={form.completion_weight}
          onChange={e => set('completion_weight', Number(e.target.value))} />
      </div>
      <div className="grid grid-cols-2 gap-3 pt-1">
        <Toggle checked={form.is_active} onChange={v => set('is_active', v)} label={t('admin', 'bf_active')} />
        <Toggle checked={form.show_in_form} onChange={v => set('show_in_form', v)} label={t('admin', 'bf_in_form')} />
        <Toggle checked={form.show_in_profile} onChange={v => set('show_in_profile', v)} label={t('admin', 'bf_in_profile')} />
        <Toggle checked={form.show_in_admin} onChange={v => set('show_in_admin', v)} label={t('admin', 'bf_in_admin')} />
      </div>
    </ModalShell>
  )
}

/* ── Field modal ───────────────────────────────────────────────────────── */

function FieldModal({ field, sectionId, inputTypes, errors, onClose }: {
  field: Field | null
  sectionId: number
  inputTypes: string[]
  errors: Record<string, string>
  onClose: () => void
}) {
  const { t } = useTranslation()
  const [processing, setProcessing] = useState(false)
  const isSystem = field?.is_system ?? false

  const [form, setForm] = useState({
    section_id: sectionId,
    field_key: field?.field_key ?? '',
    label_en: field?.label_en ?? '',
    label_bn: field?.label_bn ?? '',
    placeholder_en: field?.placeholder_en ?? '',
    placeholder_bn: field?.placeholder_bn ?? '',
    helper_text_en: field?.helper_text_en ?? '',
    helper_text_bn: field?.helper_text_bn ?? '',
    input_type: field?.input_type ?? 'text',
    options_en: (field?.options_en ?? []) as FieldOption[],
    default_value: field?.default_value ?? '',
    validation_rules: field?.validation_rules ?? '',
    is_required: field?.is_required ?? false,
    is_active: field?.is_active ?? true,
    show_in_form: field?.show_in_form ?? true,
    show_in_profile: field?.show_in_profile ?? true,
    show_in_admin: field?.show_in_admin ?? true,
    is_private: field?.is_private ?? false,
    is_searchable: field?.is_searchable ?? false,
    is_filterable: field?.is_filterable ?? false,
    cond_field: field?.conditional_logic?.field ?? '',
    cond_operator: field?.conditional_logic?.operator ?? '=',
    cond_value: (field?.conditional_logic?.value ?? '') as string | number,
  })
  const set = <K extends keyof typeof form>(k: K, v: (typeof form)[K]) => setForm(f => ({ ...f, [k]: v }))

  const showOptions = OPTION_TYPES.includes(form.input_type)

  const addOption = () => set('options_en', [...form.options_en, { value: '', label: '' }])
  const updateOption = (i: number, key: keyof FieldOption, v: string) =>
    set('options_en', form.options_en.map((o, j) => j === i ? { ...o, [key]: v } : o))
  const removeOption = (i: number) => set('options_en', form.options_en.filter((_, j) => j !== i))

  const submit = () => {
    setProcessing(true)
    const payload = {
      section_id: form.section_id,
      field_key: form.field_key,
      label_en: form.label_en,
      label_bn: form.label_bn,
      placeholder_en: form.placeholder_en,
      placeholder_bn: form.placeholder_bn,
      helper_text_en: form.helper_text_en,
      helper_text_bn: form.helper_text_bn,
      input_type: form.input_type,
      options_en: showOptions ? form.options_en : null,
      default_value: form.default_value,
      validation_rules: form.validation_rules,
      is_required: form.is_required,
      is_active: form.is_active,
      show_in_form: form.show_in_form,
      show_in_profile: form.show_in_profile,
      show_in_admin: form.show_in_admin,
      is_private: form.is_private,
      is_searchable: form.is_searchable,
      is_filterable: form.is_filterable,
      conditional_logic: form.cond_field
        ? { field: form.cond_field, operator: form.cond_operator, value: form.cond_value }
        : null,
    }
    const opts = { preserveScroll: true, onSuccess: onClose, onFinish: () => setProcessing(false) }
    if (field) router.put(route('admin.biodata-fields.fields.update', { field: field.id }), payload, opts)
    else router.post(route('admin.biodata-fields.fields.store'), payload, opts)
  }

  const selectCls = 'block w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 shadow-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500'

  return (
    <ModalShell
      title={field ? t('admin', 'bf_edit_field') : t('admin', 'bf_add_field')}
      onClose={onClose} onSubmit={submit} processing={processing} errors={errors}
    >
      {field && (
        <Input label={t('admin', 'bf_key')} value={form.field_key} disabled
          helperText={isSystem ? t('admin', 'bf_system_field_note') : t('admin', 'bf_key_immutable')} />
      )}

      <div className="grid grid-cols-2 gap-3">
        <Input label={t('admin', 'bf_label_en')} value={form.label_en} required
          onChange={e => set('label_en', e.target.value)} />
        <Input label={t('admin', 'bf_label_bn')} value={form.label_bn} required
          onChange={e => set('label_bn', e.target.value)} />
      </div>

      <div className="flex flex-col gap-1.5">
        <label className="text-sm font-medium text-slate-700">{t('admin', 'bf_input_type')}</label>
        <select className={selectCls} value={form.input_type} disabled={isSystem}
          onChange={e => set('input_type', e.target.value)}>
          {inputTypes.map(it => <option key={it} value={it}>{it}</option>)}
        </select>
        {isSystem && <p className="text-xs text-slate-400">{t('admin', 'bf_type_locked')}</p>}
      </div>

      {showOptions && (
        <div className="rounded-xl border border-slate-200 p-3">
          <div className="mb-2 flex items-center justify-between">
            <span className="text-sm font-medium text-slate-700">{t('admin', 'bf_options')}</span>
            <button type="button" onClick={addOption} className="text-sm text-primary-600 hover:text-primary-700">
              <Plus size={14} className="inline" /> {t('admin', 'bf_add_option')}
            </button>
          </div>
          <div className="space-y-2">
            {form.options_en.map((o, i) => (
              <div key={i} className="flex items-center gap-2">
                <input placeholder={t('admin', 'bf_option_value')} value={o.value}
                  onChange={e => updateOption(i, 'value', e.target.value)}
                  className="w-1/3 rounded-lg border border-slate-300 px-2 py-1.5 text-sm" />
                <input placeholder={t('admin', 'bf_option_label')} value={o.label}
                  onChange={e => updateOption(i, 'label', e.target.value)}
                  className="flex-1 rounded-lg border border-slate-300 px-2 py-1.5 text-sm" />
                <button type="button" onClick={() => removeOption(i)} className="text-red-400 hover:text-red-600">
                  <Trash2 size={14} />
                </button>
              </div>
            ))}
            {form.options_en.length === 0 && <p className="text-xs text-slate-400">{t('admin', 'bf_no_options')}</p>}
          </div>
        </div>
      )}

      <div className="grid grid-cols-2 gap-3">
        <Input label={t('admin', 'bf_placeholder_en')} value={form.placeholder_en}
          onChange={e => set('placeholder_en', e.target.value)} />
        <Input label={t('admin', 'bf_placeholder_bn')} value={form.placeholder_bn}
          onChange={e => set('placeholder_bn', e.target.value)} />
      </div>
      <div className="grid grid-cols-2 gap-3">
        <Input label={t('admin', 'bf_helper_en')} value={form.helper_text_en}
          onChange={e => set('helper_text_en', e.target.value)} />
        <Input label={t('admin', 'bf_helper_bn')} value={form.helper_text_bn}
          onChange={e => set('helper_text_bn', e.target.value)} />
      </div>
      <div className="grid grid-cols-2 gap-3">
        <Input label={t('admin', 'bf_default')} value={form.default_value}
          onChange={e => set('default_value', e.target.value)} />
        <Input label={t('admin', 'bf_rules')} value={form.validation_rules}
          helperText={t('admin', 'bf_rules_hint')}
          onChange={e => set('validation_rules', e.target.value)} />
      </div>

      <div className="grid grid-cols-2 gap-3 pt-1">
        <Toggle checked={form.is_required} onChange={v => set('is_required', v)} label={t('admin', 'bf_required')} />
        <Toggle checked={form.is_active} onChange={v => set('is_active', v)} label={t('admin', 'bf_active')} />
        <Toggle checked={form.show_in_form} onChange={v => set('show_in_form', v)} label={t('admin', 'bf_in_form')} />
        <Toggle checked={form.show_in_profile} onChange={v => set('show_in_profile', v)} label={t('admin', 'bf_in_profile')} />
        <Toggle checked={form.show_in_admin} onChange={v => set('show_in_admin', v)} label={t('admin', 'bf_in_admin')} />
        <Toggle checked={form.is_private} onChange={v => set('is_private', v)} label={t('admin', 'bf_private')} />
        <Toggle checked={form.is_filterable} onChange={v => set('is_filterable', v)} label={t('admin', 'bf_filterable')} />
        <Toggle checked={form.is_searchable} onChange={v => set('is_searchable', v)} label={t('admin', 'bf_searchable')} />
      </div>

      {/* Conditional logic */}
      <div className="rounded-xl border border-slate-200 p-3">
        <p className="mb-2 text-sm font-medium text-slate-700">{t('admin', 'bf_conditional')}</p>
        <div className="grid grid-cols-3 gap-2">
          <Input placeholder={t('admin', 'bf_cond_field')} value={form.cond_field}
            onChange={e => set('cond_field', e.target.value)} />
          <select className={selectCls} value={form.cond_operator} onChange={e => set('cond_operator', e.target.value)}>
            {['=', '!=', 'in', 'not_in', 'filled', 'empty'].map(op => <option key={op} value={op}>{op}</option>)}
          </select>
          <Input placeholder={t('admin', 'bf_cond_value')} value={String(form.cond_value)}
            onChange={e => set('cond_value', e.target.value)} />
        </div>
        <p className="mt-1.5 text-xs text-slate-400">{t('admin', 'bf_cond_hint')}</p>
      </div>
    </ModalShell>
  )
}
