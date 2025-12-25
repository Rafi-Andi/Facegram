const timeAgo = (dateString) => {
  const now = new Date()
  const past = new Date(dateString)
  const diffInSeconds = Math.floor((now - past) / 1000)

  const rtf = new Intl.RelativeTimeFormat('en', { numeric: 'auto' })

  const units = [
    { unit: 'year', seconds: 31536000 },
    { unit: 'month', seconds: 2592000 },
    { unit: 'day', seconds: 86400 },
    { unit: 'hour', seconds: 3600 },
    { unit: 'minute', seconds: 60 },
    { unit: 'second', seconds: 1 }
  ]

  for (const { unit, seconds } of units) {
    if (diffInSeconds >= seconds) {
      const value = -Math.floor(diffInSeconds / seconds)
      return rtf.format(value, unit)
    }
  }

  return 'just now'
}

export default timeAgo