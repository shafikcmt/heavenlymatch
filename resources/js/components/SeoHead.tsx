import { Head } from '@inertiajs/react'
import { useTranslation } from '@/lib/i18n'

interface Props {
  pageKey: string
  ogType?: 'website' | 'article'
}

const SITE_NAME = 'HeavenlyMatch'

export function SeoHead({ pageKey, ogType = 'website' }: Props) {
  const { t } = useTranslation()

  const title       = t('seo', `${pageKey}_title`)
  const description = t('seo', `${pageKey}_desc`)

  return (
    <Head title={title}>
      <meta name="description" content={description} />
      <meta property="og:title" content={title} />
      <meta property="og:description" content={description} />
      <meta property="og:type" content={ogType} />
      <meta property="og:site_name" content={SITE_NAME} />
      <meta name="twitter:card" content="summary_large_image" />
      <meta name="twitter:title" content={title} />
      <meta name="twitter:description" content={description} />
    </Head>
  )
}
