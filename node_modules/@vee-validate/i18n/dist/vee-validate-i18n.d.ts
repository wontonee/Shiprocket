interface FieldValidationMetaInfo {
    field: string;
    name: string;
    label?: string;
    value: unknown;
    form: Record<string, unknown>;
    rule?: {
        name: string;
        params?: Record<string, unknown> | unknown[];
    };
}
type ValidationMessageGenerator = (ctx: FieldValidationMetaInfo) => string;
type InterpolateOptions = {
    prefix: string;
    suffix: string;
};

type ValidationMessageTemplate = ValidationMessageGenerator | string;
interface PartialI18nDictionary {
    name?: string;
    messages?: Record<string, ValidationMessageTemplate>;
    names?: Record<string, string>;
    fields?: Record<string, Record<string, ValidationMessageTemplate>>;
}
type RootI18nDictionary = Record<string, PartialI18nDictionary>;
declare function localize(dictionary: RootI18nDictionary): ValidationMessageGenerator;
declare function localize(locale: string, dictionary?: PartialI18nDictionary): ValidationMessageGenerator;
declare function localize(locale: string, dictionary?: PartialI18nDictionary, interpolateOptions?: InterpolateOptions): ValidationMessageGenerator;
/**
 * Sets the locale
 */
declare function setLocale(locale: string): void;
/**
 * Sets the fallback locale.
 */
declare function setFallbackLocale(locale: string): void;
/**
 * Loads a locale file from URL and merges it with the current dictionary
 */
declare function loadLocaleFromURL(url: string): Promise<void>;

export { type FieldValidationMetaInfo, type PartialI18nDictionary, type RootI18nDictionary, type ValidationMessageTemplate, loadLocaleFromURL, localize, setFallbackLocale, setLocale };
