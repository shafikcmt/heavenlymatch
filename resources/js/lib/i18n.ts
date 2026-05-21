import { usePage } from '@inertiajs/react'

type Translations = Record<string, Record<string, unknown>>

/**
 * Replace :placeholder tokens in a translation string.
 * e.g. t('common', 'minutes_ago', { n: 5 }) → "৫ মিনিট আগে"
 */
function replacePlaceholders(value: string, replacements: Record<string, string | number>): string {
  return Object.entries(replacements).reduce<string>(
    (acc, [key, val]) => acc.replaceAll(`:${key}`, String(val)),
    value,
  )
}

function resolve(
  translations: Translations,
  namespace: string,
  key: string,
  replacements: Record<string, string | number>,
): string {
  const ns = translations[namespace]
  if (!ns) return `${namespace}.${key}`

  const parts = key.split('.')
  let node: unknown = ns

  for (const part of parts) {
    if (node === null || typeof node !== 'object') return key
    node = (node as Record<string, unknown>)[part]
  }

  if (typeof node !== 'string') return key

  return Object.keys(replacements).length > 0 ? replacePlaceholders(node, replacements) : node
}

export interface UseTranslation {
  t: (namespace: string, key: string, replacements?: Record<string, string | number>) => string
  locale: string
}

export function useTranslation(): UseTranslation {
  const { translations, locale } = usePage<{
    translations: Translations
    locale: string
    [key: string]: unknown
  }>().props

  const t = (
    namespace: string,
    key: string,
    replacements: Record<string, string | number> = {},
  ): string => resolve(translations ?? {}, namespace, key, replacements)

  return { t, locale: locale ?? 'bn' }
}
