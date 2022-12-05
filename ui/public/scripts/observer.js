const rootEl = document.querySelector("body")
const header = document.getElementById("nav-header")
const prevEntries = []
const onIntersect = (entries) => {
  // entries.sort((a, b) => a.boundingClientRect.top - b.boundingClientRect.top)
  prevEntries.shift()
  console.log(entries.map((entry) => [ entry.target.id, entry.boundingClientRect.top, entry.boundingClientRect ]))
  window.location.href = window.location.pathname + "#" + entries[0].target.id
  if (entries.length === 1) prevEntries.push(entries[0])
}
const observer = new IntersectionObserver(onIntersect, {
  root: rootEl,
  // rootMargin: "5px",
  threshold: 1, 
})

const sections = [...document.querySelectorAll("section.api-card")]
sections.forEach((section) => observer.observe(section))