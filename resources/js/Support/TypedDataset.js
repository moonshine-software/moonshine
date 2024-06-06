export default function typedDataset(dataset) {
  const result = {}

  for (let i in dataset) {
    if (dataset[i].toLowerCase() === 'true') {
      result[i] = true

      continue
    }

    if (dataset[i].toLowerCase() === 'false') {
      result[i] = false

      continue
    }

    result[i] = dataset[i]
  }

  return result
}
