declare namespace App {
namespace Data {
export type ContactData = {
id: number,
name: string,
email: string,
phone: string | null,
organizationId: number | null,
createdAt: string | string,
updatedAt: string | string,
organization: App.Data.OrganizationData | null,
tags: App.Data.TagData[],
};
export type OrganizationData = {
id: number,
name: string,
industry: string | null,
website: string | null,
};
export type TagData = {
id: number,
name: string,
};
export type UserData = {
id: number,
name: string,
email: string,
isAdmin: boolean,
emailVerifiedAt: string | string | null,
createdAt: string | string,
updatedAt: string | string,
};
}
}
