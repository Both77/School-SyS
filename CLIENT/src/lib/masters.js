import { api } from "./api";

export async function fetchMasters() {
  const [classesRes, sectionsRes, subjectsRes] = await Promise.all([
    api.get("/classes"),
    api.get("/sections"),
    api.get("/subjects"),
  ]);
  return {
    classes: classesRes.data,
    sections: sectionsRes.data,
    subjects: subjectsRes.data,
  };
}
